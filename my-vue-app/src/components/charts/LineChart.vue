<template>
  <div class="chart-container">
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script>
import { Chart, LineElement, PointElement, LineController, CategoryScale, LinearScale, Tooltip, Legend, Filler } from 'chart.js';

// Register required Chart.js components individually
Chart.register(
  LineElement, 
  PointElement,
  LineController,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend,
  Filler
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
    // Ensure DOM is fully updated before rendering
    this.$nextTick(() => {
      // Small delay to ensure the canvas is ready
      setTimeout(() => {
        this.renderChart();
      }, 0);
    });
  },
  watch: {
    chartData: {
      handler() {
        // Only update if chart exists
        if (this.chart) {
          this.updateChart();
        } else {
          this.renderChart();
        }
      },
      deep: true
    },
    options: {
      handler() {
        if (this.chart) {
          this.updateChart();
        }
      },
      deep: true
    }
  },
  methods: {    renderChart() {
      // Safety check to ensure component is still mounted
      if (!this.$refs.canvas) {
        console.warn('LineChart: Canvas element not found');
        return;
      }
      
      const canvas = this.$refs.canvas;
      
      // Make sure canvas is properly sized
      if (canvas.width === 0 || canvas.height === 0) {
        // Delay rendering if canvas has no size
        setTimeout(() => this.renderChart(), 100);
        return;
      }
      
      // Get canvas context
      let ctx;
      try {
        ctx = canvas.getContext('2d');
        
        // Test that the context is valid and has the required methods
        if (!ctx || typeof ctx.save !== 'function' || typeof ctx.clip !== 'function') {
          console.error('LineChart: Invalid canvas context - missing required methods');
          return;
        }
      } catch (err) {
        console.error('LineChart: Failed to get canvas context', err);
        return;
      }
      
      // Destroy previous chart instance if it exists
      if (this.chart) {
        try {
          this.chart.destroy();
        } catch (err) {
          console.error('LineChart: Error destroying previous chart instance', err);
        }
        this.chart = null;
      }
      
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
        },
        // Fix for event handling issue
        events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
        interaction: {
          mode: 'nearest',
          intersect: true,
        }
      };
      
      try {
        // Create new chart with safely merged options
        const mergedOptions = this.mergeDeep(defaultOptions, this.options || {});        // Test context is still valid before creating chart
        if (!ctx || typeof ctx.save !== 'function' || typeof ctx.clip !== 'function') {
          console.error('LineChart: Canvas context lost before chart creation, aborting');
          return;
        }

        // Create the chart with error handling
        try {
          // Ensure the options properly handle all properties needed for clipArea
          const safeOptions = {
            ...mergedOptions,
            // Ensure animation is handled properly
            animation: {
              duration: 600,
              onComplete: function() {
                // Ensure context is valid before any clips are drawn
                if (ctx && typeof ctx.save === 'function' && typeof ctx.clip === 'function') {
                  // Context is valid
                } else {
                  console.warn('LineChart: Canvas context is invalid in animation callback');
                }
              }
            },
            // Fix for the "disabled" property issue
            elements: {
              ...(mergedOptions.elements || {}),
              line: {
                ...(mergedOptions.elements?.line || {}),
                fill: true, 
                tension: 0.4
              }
            },
            plugins: {
              ...(mergedOptions.plugins || {}),
              // Explicitly define the filler plugin configuration
              filler: {
                propagate: true,
                drawTime: 'beforeDatasetDraw',
                // This is important to avoid the 'disabled' property error
                drawArea: null  // Let Chart.js use the default implementation
              }
            }
          };

          // Create a clean new Chart instance
          this.chart = new Chart(ctx, {
            type: 'line',
            data: validatedData,
            options: safeOptions,
            plugins: [] // Ensure plugins array is defined
          });
        } catch (error) {
          console.error('Chart creation error:', error);
          // Try again with minimum options after a short delay
          setTimeout(() => {
            try {
              if (ctx && typeof ctx.save === 'function' && typeof ctx.clip === 'function') {
                this.chart = new Chart(ctx, {
                  type: 'line',
                  data: validatedData,
                  options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
                    interaction: {
                      mode: 'nearest',
                      intersect: false
                    },
                    animation: {
                      duration: 300 // Shorter animation for fallback
                    }
                  },
                  plugins: []
                });
              }
            } catch (fallbackError) {
              console.error('Fallback chart creation failed:', fallbackError);
            }
          }, 100);
        }
      } catch (error) {
        console.error('Chart initialization error:', error);
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

      try {
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
      } catch (error) {
        console.error('Error validating chart data:', error);
        
        // Return safe fallback data
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
    },
      updateChart() {
      if (!this.chart) {
        this.renderChart();
        return;
      }
      
      try {
        // Verify canvas and context are still valid
        if (!this.$refs.canvas) {
          console.warn('LineChart: Canvas element no longer exists during update');
          return this.renderChart();
        }
        
        const ctx = this.$refs.canvas.getContext('2d');
        if (!ctx) {
          console.error('LineChart: Failed to get canvas context during update');
          return this.renderChart();
        }
        
        // Test that the context has required methods
        if (typeof ctx.save !== 'function' || typeof ctx.clip !== 'function') {
          console.error('LineChart: Canvas context methods missing during update');
          return this.renderChart();
        }
        
        const validatedData = this.validateChartData(this.chartData);
        
        // Ensure proper plugin configuration to avoid the 'disabled' property error
        if (!this.chart.options.plugins) {
          this.chart.options.plugins = {};
        }
        
        // Make sure filler plugin configuration exists to prevent 'disabled' property errors
        if (!this.chart.options.plugins.filler) {
          this.chart.options.plugins.filler = {
            propagate: true,
            drawTime: 'beforeDatasetDraw',
            drawArea: null  // Let Chart.js use the default implementation
          };
        }
        
        // Update the chart data
        this.chart.data = validatedData;
        
        // Use 'default' mode instead of 'none' to ensure proper context handling
        this.chart.update('default');
      } catch (error) {
        console.error('Chart update error:', error);
        // If update fails, destroy and re-create the chart
        this.$nextTick(() => {
          if (this.chart) {
            this.chart.destroy();
            this.chart = null;
          }
          setTimeout(() => {
            this.renderChart(); 
          }, 0);
        });
      }
    }
  },
  beforeUnmount() {
    // Clean up chart instance
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