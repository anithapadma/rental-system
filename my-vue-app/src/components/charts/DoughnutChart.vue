<template>
  <div class="chart-container">
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script>
import { Chart, ArcElement, DoughnutController, Tooltip, Legend, Filler } from 'chart.js';

// Register required Chart.js components individually
Chart.register(
  ArcElement,
  DoughnutController,
  Tooltip,
  Legend,
  Filler
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
        console.warn('DoughnutChart: Canvas element not found');
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
          console.error('DoughnutChart: Invalid canvas context - missing required methods');
          return;
        }
      } catch (err) {
        console.error('DoughnutChart: Failed to get canvas context', err);
        return;
      }
      
      // Destroy previous chart instance if it exists
      if (this.chart) {
        try {
          this.chart.destroy();
        } catch (err) {
          console.error('DoughnutChart: Error destroying previous chart instance', err);
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
            top: 5,
            right: 5, 
            bottom: 5,
            left: 5
          }
        },
        // Add events configuration to prevent the 'cannot read properties of undefined (includes)' error
        events: ['mousemove', 'mouseout', 'click', 'touchstart', 'touchmove'],
        interaction: {
          mode: 'nearest',
          intersect: true
        },
        elements: {
          arc: {
            borderWidth: 1
          }
        },
        animation: {
          animateRotate: true,
          animateScale: true,
          duration: 800
        },
        plugins: {
          legend: {
            position: 'right',
            align: 'center',
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
            displayColors: true,
            callbacks: {
              label: (context) => {
                const label = context.label || '';
                const value = context.raw || 0;
                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                const percentage = Math.round((value / total) * 100);
                return `${label}: ${value} (${percentage}%)`;
              }
            }
          }
        },
        cutout: '65%'
      };
      
      try {
        // Create new chart with safely merged options
        const safeOptions = this.options || {};
        const mergedOptions = this.mergeDeep(defaultOptions, safeOptions);
          // Test context is still valid before creating chart
        if (!ctx || typeof ctx.save !== 'function' || typeof ctx.clip !== 'function') {
          console.error('DoughnutChart: Canvas context lost before chart creation, aborting');
          return;
        }

        // Create the chart with error handling
        try {          // Ensure the options properly handle all properties needed for clipArea
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
                  console.warn('DoughnutChart: Canvas context is invalid in animation callback');
                }
              }
            },
            // Fix for the "disabled" property issue
            elements: {
              ...(mergedOptions.elements || {}),
              arc: {
                ...(mergedOptions.elements?.arc || {}),
                borderWidth: 1
              }
            },
            plugins: {
              ...(mergedOptions.plugins || {}),
              // Explicitly define the filler plugin configuration to avoid 'disabled' error
              filler: {
                propagate: false,
                // This is important to avoid the 'disabled' property error
                drawArea: null  // Let Chart.js use the default implementation
              }
            }
          };
          
          // Create a clean new Chart instance
          this.chart = new Chart(ctx, {
            type: 'doughnut',
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
                  type: 'doughnut',
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
          labels: ['Category 1', 'Category 2', 'Category 3', 'Category 4'],
          datasets: [{
            data: [25, 25, 25, 25],
            backgroundColor: [
              'rgba(66, 153, 225, 0.8)',
              'rgba(72, 187, 120, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)'
            ],
            hoverBackgroundColor: [
              'rgba(66, 153, 225, 1)',
              'rgba(72, 187, 120, 1)',
              'rgba(237, 137, 54, 1)',
              'rgba(159, 122, 234, 1)'
            ]
          }]
        };
      }
      
      try {
        // Create a deep copy to avoid mutating props
        const result = JSON.parse(JSON.stringify(data));
        
        // Ensure labels exist and are strings
        if (!result.labels || !Array.isArray(result.labels) || result.labels.length === 0) {
          result.labels = ['Category 1', 'Category 2', 'Category 3', 'Category 4'];
        }
        
        // Ensure datasets exist and are properly formatted
        if (!result.datasets || !Array.isArray(result.datasets) || result.datasets.length === 0) {
          result.datasets = [{
            data: [25, 25, 25, 25],
            backgroundColor: [
              'rgba(66, 153, 225, 0.8)',
              'rgba(72, 187, 120, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)'
            ],
            hoverBackgroundColor: [
              'rgba(66, 153, 225, 1)',
              'rgba(72, 187, 120, 1)',
              'rgba(237, 137, 54, 1)',
              'rgba(159, 122, 234, 1)'
            ]
          }];
        } else {
          // Validate each dataset
          result.datasets = result.datasets.map(dataset => {
            const validDataset = { ...dataset };
            
            // Ensure data is an array and not empty
            if (!Array.isArray(validDataset.data) || validDataset.data.length === 0) {
              validDataset.data = [25, 25, 25, 25];
            } else {
              // Make sure all values are numbers
              validDataset.data = validDataset.data.map(val => 
                typeof val === 'number' && !isNaN(val) ? val : 0
              );
              
              // If all values are 0, provide dummy data
              if (validDataset.data.every(val => val === 0)) {
                validDataset.data = [25, 25, 25, 25];
              }
            }
            
            // Ensure backgroundColor is an array and has enough colors
            const defaultColors = [
              'rgba(66, 153, 225, 0.8)',
              'rgba(72, 187, 120, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)',
              'rgba(246, 173, 85, 0.8)',
              'rgba(245, 101, 101, 0.8)',
              'rgba(79, 209, 197, 0.8)',
            ];
            
            if (!Array.isArray(validDataset.backgroundColor) || 
                validDataset.backgroundColor.length < validDataset.data.length) {
              // Use as many colors from the dataset as available
              const existingColors = Array.isArray(validDataset.backgroundColor) 
                ? validDataset.backgroundColor 
                : [];
              
              validDataset.backgroundColor = [
                ...existingColors,
                ...defaultColors.slice(existingColors.length)
              ].slice(0, validDataset.data.length);
            }
            
            // Ensure hoverBackgroundColor matches backgroundColor but at full opacity
            if (!Array.isArray(validDataset.hoverBackgroundColor) || 
                validDataset.hoverBackgroundColor.length < validDataset.data.length) {
              validDataset.hoverBackgroundColor = validDataset.backgroundColor.map(color => {
                // Replace opacity value with 1 in rgba()
                return color.replace(/rgba\((\d+),\s*(\d+),\s*(\d+),\s*[\d.]+\)/, 'rgba($1, $2, $3, 1)');
              });
            }
            
            return validDataset;
          });
        }
        
        return result;
      } catch (error) {
        console.error('Error validating chart data:', error);
        
        // Return safe fallback data
        return {
          labels: ['Category 1', 'Category 2', 'Category 3', 'Category 4'],
          datasets: [{
            data: [25, 25, 25, 25],
            backgroundColor: [
              'rgba(66, 153, 225, 0.8)',
              'rgba(72, 187, 120, 0.8)',
              'rgba(237, 137, 54, 0.8)',
              'rgba(159, 122, 234, 0.8)'
            ],
            hoverBackgroundColor: [
              'rgba(66, 153, 225, 1)',
              'rgba(72, 187, 120, 1)',
              'rgba(237, 137, 54, 1)',
              'rgba(159, 122, 234, 1)'
            ]
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
          console.warn('DoughnutChart: Canvas element no longer exists during update');
          return this.renderChart();
        }
        
        const ctx = this.$refs.canvas.getContext('2d');
        if (!ctx) {
          console.error('DoughnutChart: Failed to get canvas context during update');
          return this.renderChart();
        }
        
        // Test that the context has required methods
        if (typeof ctx.save !== 'function' || typeof ctx.clip !== 'function') {
          console.error('DoughnutChart: Canvas context methods missing during update');
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
            propagate: false,
            drawTime: 'beforeDatasetDraw',
            drawArea: null  // Let Chart.js use the default implementation
          };
        }
        
        // Update chart data safely
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
  min-height: 250px;
  max-height: 400px;
}
</style>