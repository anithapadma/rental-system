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
      handler(newVal) {
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
      if (!this.$refs.canvas) {
        console.warn('DoughnutChart: Canvas element not found');
        return;
      }
      
      const ctx = this.$refs.canvas.getContext('2d');
      if (!ctx) {
        console.error('DoughnutChart: Failed to get canvas context');
        return;
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
      
      // Ensure the chart is destroyed before creating a new one
      if (this.chart) {
        this.chart.destroy();
      }
      
      try {
        // Create new chart with safely merged options
        const safeOptions = this.options || {};
        const mergedOptions = this.mergeDeep(defaultOptions, safeOptions);
        
        this.chart = new Chart(ctx, {
          type: 'doughnut',
          data: validatedData,
          options: mergedOptions
        });
      } catch (error) {
        console.error('Chart creation error:', error);
        // Try again with minimum options
        try {
          this.chart = new Chart(ctx, {
            type: 'doughnut',
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
        const validatedData = this.validateChartData(this.chartData);
        
        // Update chart data
        this.chart.data.labels = validatedData.labels;
        this.chart.data.datasets = validatedData.datasets;
        
        // Update chart
        this.chart.update();
      } catch (error) {
        console.error('Chart update error:', error);
        // If update fails, try re-rendering the chart
        this.$nextTick(() => {
          this.renderChart();
        });
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
  min-height: 250px;
  max-height: 400px;
}
</style>