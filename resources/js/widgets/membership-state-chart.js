'use strict';
document.addEventListener('DOMContentLoaded', function () {
  setTimeout(function () {
    // Detect theme from body attribute
    var currentTheme = document.getElementsByTagName('body')[0].getAttribute('data-pc-theme') || 'light';
    var chartColor = currentTheme === 'dark' ? '#9c27b0' : '#28a745'; // Purple for dark, green for light
    var trackColor = currentTheme === 'dark' ? '#9c27b025' : '#28a74525'; // Purple for dark, green for light
    
    var membership_state_chart_option = {
      series: [76],
      chart: {
        type: 'radialBar',
        offsetY: -20,
        sparkline: {
          enabled: true
        }
      },
      colors: [chartColor],
      plotOptions: {
        radialBar: {
          startAngle: -95,
          endAngle: 95,
          hollow: {
            margin: 15,
            size: '40%'
          },
          track: {
            background: trackColor,
            strokeWidth: '97%',
            margin: 10
          },
          dataLabels: {
            name: {
              show: false
            },
            value: {
              offsetY: 0,
              fontSize: '20px'
            }
          }
        }
      },
      grid: {
        padding: {
          top: 10
        }
      },
      stroke: {
        lineCap: 'round'
      },
      labels: ['Average Results']
    };
    var chart = new ApexCharts(document.querySelector('#membership-state-chart'), membership_state_chart_option);
    chart.render();
    
    // Watch for theme changes and update chart colors
    var observer = new MutationObserver(function(mutations) {
      mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'data-pc-theme') {
          var newTheme = document.getElementsByTagName('body')[0].getAttribute('data-pc-theme') || 'light';
          var newChartColor = newTheme === 'dark' ? '#9c27b0' : '#28a745';
          var newTrackColor = newTheme === 'dark' ? '#9c27b025' : '#28a74525';
          
          chart.updateOptions({
            colors: [newChartColor],
            plotOptions: {
              radialBar: {
                track: {
                  background: newTrackColor
                }
              }
            }
          });
        }
      });
    });
    
    // Start observing the body element for theme changes
    observer.observe(document.getElementsByTagName('body')[0], {
      attributes: true,
      attributeFilter: ['data-pc-theme']
    });
  }, 500);
});
