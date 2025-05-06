<template>
  <div class="chart-container">
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script>
import { Chart, BarElement, BarController, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';

// Register required Chart.js components individually
Chart.register(
  BarElement,
  BarController,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
);

export default {
  name: 'BarChart',
  props: {
    chartData: {
      type: Object,
      required: true
    },
    options: {
      type: Object,
      default: () => ({})
    }
  },
  data() {
    return {
      chart: null
    };
  },
  mounted() {
    this.$nextTick(() => {
      this.renderChart();
    });
  },
  watch: {
    chartData: {
      handler() {
        this.updateChart();
      },
      deep: true
    },
    options: {
      handler() {
        this.updateChart();
      },
      deep: true
    }
  },
  methods: {
    renderChart() {
      if (!this.$refs.canvas) return;
      
      const ctx = this.$refs.canvas.getContext('2d');
      
      // Verify and fix chartData to ensure it has proper structure
      const validatedData = this.validateChartData(this.chartData);
      
      // Default options with professional styling
      const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              boxWidth: 10,
              usePointStyle: true,
              font: {
                family: "'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif",
                size: 11
              }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(26, 54, 93, 0.9)',
            titleFont: {
              size: 12,
              family: "'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif",
              weight: 'bold'
            },
            bodyFont: {
              size: 11,
              family: "'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif"
            },
            cornerRadius: 4,
            padding: 8
          }
        },
        scales: {
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                family: "'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif",
                size: 10
              }
            }
          },
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
              font: {
                family: "'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif",
                size: 10
              }
            }
          }
        },
        barPercentage: 0.7,
        categoryPercentage: 0.8
      };
      
      // Ensure the chart is destroyed before creating a new one
      if (this.chart) {
        this.chart.destroy();
      }
      
      try {
        // Create new chart
        this.chart = new Chart(ctx, {
          type: 'bar',
          data: validatedData,
          options: defaultOptions
        });
      } catch (error) {
        console.error('Chart creation error:', error);
      }
    },
    validateChartData(data) {
      // Create a deep copy to avoid mutating props
      const result = JSON.parse(JSON.stringify(data));
      
      // Ensure labels exist and are strings
      if (!result.labels || !Array.isArray(result.labels)) {
        result.labels = ['Item 1', 'Item 2', 'Item 3', 'Item 4', 'Item 5'];
      }
      
      // Ensure datasets exist and are properly formatted
      if (!result.datasets || !Array.isArray(result.datasets)) {
        result.datasets = [];
      }
      
      result.datasets = result.datasets.map(dataset => {
        const validDataset = { ...dataset };
        
        // Ensure dataset label is a string
        if (typeof validDataset.label !== 'string') {
          validDataset.label = 'Data';
        }
        
        // Ensure data is an array
        if (!Array.isArray(validDataset.data)) {
          validDataset.data = [0, 0, 0, 0, 0];
        }
        
        // Ensure backgroundColor is valid
        if (typeof validDataset.backgroundColor !== 'string' && !Array.isArray(validDataset.backgroundColor)) {
          validDataset.backgroundColor = 'rgba(66, 153, 225, 0.8)';
        }
        
        // Ensure borderColor is valid
        if (typeof validDataset.borderColor !== 'string' && !Array.isArray(validDataset.borderColor)) {
          validDataset.borderColor = 'rgba(66, 153, 225, 1)';
        }
        
        return validDataset;
      });
      
      return result;
    },
    updateChart() {
      if (!this.chart) {
        this.renderChart();
        return;
      }
      
      try {
        const validatedData = this.validateChartData(this.chartData);
        this.chart.data = validatedData;
        this.chart.update('none'); // Use 'none' to prevent animation during update
      } catch (error) {
        console.error('Chart update error:', error);
      }
    }
  },
  beforeUnmount() {
    if (this.chart) {
      this.chart.destroy();
      this.chart = null;
    }
  }
}
</script>

<style scoped>
.chart-container {
  position: relative;
  width: 100%;
  height: 100%;
  min-height: 200px;
}
</style>