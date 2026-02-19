<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("🔵 dashboard_scripts loaded");

        // Register the ChartDataLabels plugin
        Chart.register(ChartDataLabels);

        const orderStatusCanvas = document.getElementById('orderStatusChart');
        if (orderStatusCanvas) {
            const orderStatuses = @json($orderStatuses);

            // 1) نخزن المفاتيح الأصلية (الإنجليزي)
            const statusKeys = Object.keys(orderStatuses); // مثال: ['Pending', 'Completed', ...]

            // 2) القيم كما هي
            const dataValues = Object.values(orderStatuses);

            // 3) خريطة الأسماء بالعربي
            const statusLabelMap = {
                "new_order": "طلب جديد",
                "needs_modification": "يوجد تعديل",
                "Pending": "تم التصميم",
                "Completed": "تم الاعتماد",
                "preparing": "قيد التجهيز",
                "Received": "تم التسليم",
                "Out for Delivery": "مرتجع",
                "Canceled": "رفض الاستلام"
            };

            // 4) اللابلز المستخدمة في التشارت (عربي)
            const labels = statusKeys.map(function(key) {
                return statusLabelMap[key] || key;
            });

            // 5) خريطة الألوان (نفس ألوان الحالات)
            const statusColorMap = {
         "new_order": "#0d6efd",           // أزرق (Primary)
                "needs_modification": "#dc3545",  // أحمر (Danger)
                "Pending": "#ffc107",             // أصفر (Warning)
                "Completed": "#0dcaf0",           // سماوي (Info)
                "preparing": "#6f42c1",           // بنفسجي (Purple)
                "Received": "#198754",            // أخضر (Success)
                "Out for Delivery": "#fd7e14",    // برتقالي (Orange)
                "Canceled": "#800000"
            };

            // 6) ألوان الهفر (ممكن نخليها نفس اللون)
            const statusHoverColorMap = statusColorMap;


            new Chart(orderStatusCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataValues,
                        // نستخدم statusKeys عشان الألوان تعتمد على المفتاح الإنجليزي
                        backgroundColor: statusKeys.map(function(key) {
                            return statusColorMap[key] || "rgba(153,153,153,0.8)";
                        }),
                        borderWidth: 0,
                        hoverBackgroundColor: statusKeys.map(function(key) {
                            return statusHoverColorMap[key] || "rgba(153,153,153,1)";
                        }),
                        hoverBorderColor: statusKeys.map(function(key) {
                            return statusHoverColorMap[key] || "rgba(153,153,153,1)";
                        }),
                        hoverBorderWidth: 0
                    }]

                },
                options: {
                    responsive: true,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                color: '#000',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: function(context) {
                                const index = context.tooltip.dataPoints[0].dataIndex;
                                const dataset = context.tooltip.dataPoints[0].dataset;
                                return dataset.backgroundColor[index];
                            },
                            borderColor: '#fff', // White border color
                            borderWidth: 1, // Adjust the width as needed
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = total ? ((value / total) * 100).toFixed(1) + '%' : '0%';
                                    return `${label}: ${value} (${percentage})`;
                                }
                            }
                        },
                        datalabels: {
                            color: '#fff',
                            font: {
                                weight: 'bold',
                                size: 14
                            },
                            formatter: (value, context) => {
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = total ? ((value / total) * 100).toFixed(1) : 0;
                                return percentage + '%';
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        duration: 1200,
                        easing: 'easeInOutBack'
                    }
                }
            });
        }

        // --- Orders With/Without Additives Bar Chart ---
        const additivesCanvas = document.getElementById('additivesChart');
        if (additivesCanvas) {
            const ctx = additivesCanvas.getContext('2d');
            // Create gradient fills using the canvas height
            const gradientBlue = ctx.createLinearGradient(0, 0, 0, additivesCanvas.height);
            gradientBlue.addColorStop(0, 'rgba(0, 123, 255, 0.8)');
            gradientBlue.addColorStop(1, 'rgba(0, 123, 255, 0.4)');

            const gradientRed = ctx.createLinearGradient(0, 0, 0, additivesCanvas.height);
            gradientRed.addColorStop(0, 'rgba(220, 53, 69, 0.8)');
            gradientRed.addColorStop(1, 'rgba(220, 53, 69, 0.4)');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ["With Additives", "Without Additives"],
                    datasets: [{
                        data: [@json($ordersWithAdditives), @json($ordersWithoutAdditives)],
                        backgroundColor: [gradientBlue, gradientRed],
                        borderColor: ['#007bff', '#dc3545'],
                        borderWidth: 1,
                        borderRadius: 8,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#333',
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#333',
                                font: {
                                    size: 12
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.7)',
                            titleFont: {
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 10,
                            cornerRadius: 4,
                            displayColors: false
                        },
                        datalabels: {
                            color: '#fff',
                            anchor: 'end',
                            align: 'top',
                            font: {
                                weight: 'bold',
                                size: 12
                            },
                            formatter: (value) => value
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }

        // --- Top Selling Products Chart (Vertical Bar Chart) ---
        const topSellingCanvas = document.getElementById('topSellingChart');
        if (topSellingCanvas) {
            new Chart(topSellingCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!!json_encode($topSellingProducts -> pluck('bookType.name_ar')) !!},
                    datasets: [{
                        label: 'Orders',
                        data: {!!json_encode($topSellingProducts -> pluck('total_orders')) !!},
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#333'
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#333',
                                autoSkip: false
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.7)',
                            titleFont: {
                                weight: 'bold'
                            },
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y;
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#333',
                            font: {
                                weight: 'bold'
                            },
                            formatter: (value) => value
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuad'
                    }
                }
            });
        }

        // --- Orders by School Chart ---
        const schoolCanvas = document.getElementById('schoolChart');
        if (schoolCanvas) {
            const schoolLabels = {!!json_encode($ordersBySchool -> pluck('school_label')) !!};
            const schoolData = {!!json_encode($ordersBySchool -> pluck('total_orders')) !!};

            new Chart(schoolCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: schoolLabels,
                    datasets: [{
                        label: 'Orders',
                        data: schoolData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#333'
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#333',
                                autoSkip: false
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.7)',
                            titleFont: {
                                weight: 'bold'
                            },
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y;
                                }
                            }
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#333',
                            font: {
                                weight: 'bold'
                            },
                            formatter: (value) => value
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuad'
                    }
                }
            });
        }
    });
</script>