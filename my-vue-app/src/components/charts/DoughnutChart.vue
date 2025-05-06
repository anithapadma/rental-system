<template>
  <div class="chart-container">
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script>
import { Chart, ArcElement, DoughnutController, Tooltip, Legend } from 'chart.js';

// Register required Chart.js components individually
Chart.register(
  ArcElement,
  DoughnutController,
  Tooltip,
  Legend
);

export default {
  name: 'DoughnutChart',
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
            position: 'right',
            labels: {
              boxWidth: 10,
              usePointStyle: true,
              font: {
                family: "'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif",
                size: 11
              },
              padding: 15
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
        cutout: '65%', // Makes it a doughnut chart
        borderWidth: 1
      };
      
      // Ensure the chart is destroyed before creating a new one
      if (this.chart) {
        this.chart.destroy();
      }
      
      try {
        // Create new chart
        this.chart = new Chart(ctx, {
          type: 'doughnut',
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
        result.labels = ['Category 1', 'Category 2', 'Category 3', 'Category 4'];
      }
      
      // Ensure datasets exist and are properly formatted
      if (!result.datasets || !Array.isArray(result.datasets)) {
        result.datasets = [{
          data: [25, 25, 25, 25],
          backgroundColor: [
            'rgba(66, 153, 225, 0.8)',
            'rgba(72, 187, 120, 0.8)',
            'rgba(237, 137, 54, 0.8)',
            'rgba(159, 122, 234, 0.8)'
          ]
        }];
      } else {
        // Validate each dataset
        result.datasets = result.datasets.map(dataset => {
          const validDataset = { ...dataset };
          
          // Ensure data is an array
          if (!Array.isArray(validDataset.data)) {
            validDataset.data = [25, 25, 25, 25];
          }
          
          // Ensure backgroundColor is valid
          if (!Array.isArray(validDataset.backgroundColor)) {
            validDataset.backgroundColor = [
              'rgba(66, 153, 225, 0.8)',
              'rgba(72, 187, 120, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)'
            ];
          }
          
          // Ensure hoverBackgroundColor is valid
          if (validDataset.hoverBackgroundColor && !Array.isArray(validDataset.hoverBackgroundColor)) {
            delete validDataset.hoverBackgroundColor;
          }
          
          return validDataset;
        });
      }
      
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
  min-height: 180px;
}
</style>