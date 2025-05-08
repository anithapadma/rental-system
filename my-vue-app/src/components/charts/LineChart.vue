<template>
  <div class="chart-container">
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script>
import { Chart, LineElement, PointElement, LineController, CategoryScale, LinearScale, Tooltip, Legend } from 'chart.js';

// Register required Chart.js components individually
Chart.register(
  LineElement, 
  PointElement,
  LineController,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
);

export default {
  name: 'LineChart',
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
    this.renderChart();
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
        layout: {
          padding: {
            top: 10,
            right: 15, 
            bottom: 10,
            left: 15
          }
        },
        plugins: {
          legend: {
            position: 'top',
            align: 'start',
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
            enabled: true,
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
            padding: 8,
            displayColors: true
          }
        },
        scales: {
          x: {
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
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
        elements: {
          line: {
            tension: 0.4
          },
          point: {
            radius: 4,
            hoverRadius: 6
          }
        }
      };
      
      // Ensure the chart is destroyed before creating a new one
      if (this.chart) {
        this.chart.destroy();
      }
      
      try {
        // Create new chart with merged options
        const mergedOptions = this.mergeDeep(defaultOptions, this.options || {});
        
        this.chart = new Chart(ctx, {
          type: 'line',
          data: validatedData,
          options: mergedOptions
        });
      } catch (error) {
        console.error('Chart creation error:', error);
        // Try again with minimum options
        try {
          this.chart = new Chart(ctx, {
            type: 'line',
            data: validatedData,
            options: {
              responsive: true,
              maintainAspectRatio: false
            }
          });
        } catch (fallbackError) {
          console.error('Fallback chart creation failed:', fallbackError);
        }
      }
    },

    mergeDeep(target, source) {
      // For merging options objects deeply
      const isObject = obj => obj && typeof obj === 'object' && !Array.isArray(obj);
      
      if (!isObject(target) || !isObject(source)) {
        return source;
      }
      
      const output = {...target};
      
      Object.keys(source).forEach(key => {
        if (isObject(source[key])) {
          if (!(key in target)) {
            output[key] = source[key];
          } else {
            output[key] = this.mergeDeep(target[key], source[key]);
          }
        } else {
          output[key] = source[key];
        }
      });
      
      return output;
    },

    validateChartData(data) {
      // Handle null or undefined data
      if (!data) {
        return {
          labels: ['Item 1', 'Item 2', 'Item 3', 'Item 4', 'Item 5'],
          datasets: [{
            label: 'Data',
            data: [0, 0, 0, 0, 0],
            borderColor: '#4299e1',
            backgroundColor: 'rgba(66, 153, 225, 0.1)',
            borderWidth: 2,
            fill: true
          }]
        };
      }

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
        
        // Ensure borderColor is a string
        if (typeof validDataset.borderColor !== 'string') {
          validDataset.borderColor = '#4299e1';
        }
        
        // Ensure backgroundColor is a string
        if (typeof validDataset.backgroundColor !== 'string') {
          validDataset.backgroundColor = 'rgba(66, 153, 225, 0.1)';
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
        this.chart.update();
      } catch (error) {
        console.error('Chart update error:', error);
        this.renderChart(); // Try re-rendering the chart on update error
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
  min-height: 220px;
}
</style>