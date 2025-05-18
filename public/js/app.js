const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

allSideMenu.forEach(item => {
    const li = item.parentElement;

    item.addEventListener('click', function () {
        allSideMenu.forEach(i => {
            i.parentElement.classList.remove('active');
        })
        li.classList.add('active');
    })
});

// TOGGLE SIDEBAR
const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
    sidebar.classList.toggle('hide');
});

// Responsive sidebar
function adjustSidebar() {
    if (window.innerWidth <= 576) {
        sidebar.classList.add('hide');
        sidebar.classList.remove('show');
    } else {
        sidebar.classList.remove('hide');
        sidebar.classList.add('show');
    }
}

window.addEventListener('load', adjustSidebar);
window.addEventListener('resize', adjustSidebar);

// Search functionality
const searchButton = document.querySelector('#content nav form .form-input button');
const searchButtonIcon = document.querySelector('#content nav form .form-input button .bx');
const searchForm = document.querySelector('#content nav form');

searchButton.addEventListener('click', function (e) {
    if (window.innerWidth < 768) {
        e.preventDefault();
        searchForm.classList.toggle('show');
        if (searchForm.classList.contains('show')) {
            searchButtonIcon.classList.replace('bx-search', 'bx-x');
        } else {
            searchButtonIcon.classList.replace('bx-x', 'bx-search');
        }
    }
})

// Dark Mode Toggle
const switchMode = document.getElementById('switch-mode');

switchMode.addEventListener('change', function () {
    if (this.checked) {
        document.body.classList.add('dark');
    } else {
        document.body.classList.remove('dark');
    }
})

// Notification Menu
document.querySelector('.notification').addEventListener('click', function () {
    document.querySelector('.notification-menu').classList.toggle('show');
    document.querySelector('.profile-menu').classList.remove('show');
});

// Profile Menu
document.querySelector('.profile').addEventListener('click', function () {
    document.querySelector('.profile-menu').classList.toggle('show');
    document.querySelector('.notification-menu').classList.remove('show');
});

// Close menus when clicking outside
window.addEventListener('click', function (e) {
    if (!e.target.closest('.notification') && !e.target.closest('.profile')) {
        document.querySelector('.notification-menu').classList.remove('show');
        document.querySelector('.profile-menu').classList.remove('show');
    }
});

// Sample data for dashboard (replace with real data from your backend)
document.addEventListener('DOMContentLoaded', function() {
    // These would normally come from your Laravel backend
    document.getElementById('today-orders').textContent = '12';
    document.getElementById('today-sales').textContent = 'KSh 24,850';
    document.getElementById('conversion-rate').textContent = '3.2%';
});
document.addEventListener('DOMContentLoaded', function() {
    // Sample data - replace with real data from your backend
    const analyticsData = {
        dates: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        profits: [12000, 19000, 15000, 22000, 18000, 25000, 28000],
        losses: [4000, 3000, 5000, 2000, 6000, 3000, 2000],
        newCustomers: [15, 20, 18, 25, 22, 30, 28],
        returningCustomers: [10, 12, 15, 18, 20, 22, 25],
        topProducts: ['Soap', 'Candles', 'Baskets', 'Jewelry', 'Clothing'],
        productSales: [120, 85, 60, 45, 30],
        funnelStages: ['Visitors', 'Add to Cart', 'Checkout', 'Purchases'],
        funnelData: [1000, 400, 200, 120]
    };

    // Profit/Loss Chart
    const profitLossCtx = document.getElementById('profitLossChart').getContext('2d');
    new Chart(profitLossCtx, {
        type: 'bar',
        data: {
            labels: analyticsData.dates,
            datasets: [
                {
                    label: 'Profit',
                    data: analyticsData.profits,
                    backgroundColor: '#4ade80',
                    borderRadius: 4
                },
                {
                    label: 'Loss',
                    data: analyticsData.losses,
                    backgroundColor: '#f87171',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.dataset.label}: KSh ${context.raw.toLocaleString()}`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'KSh ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Customer Acquisition Chart
    const customerCtx = document.getElementById('customerChart').getContext('2d');
    new Chart(customerCtx, {
        type: 'line',
        data: {
            labels: analyticsData.dates,
            datasets: [
                {
                    label: 'New Customers',
                    data: analyticsData.newCustomers,
                    borderColor: '#60a5fa',
                    backgroundColor: 'rgba(96, 165, 250, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Returning Customers',
                    data: analyticsData.returningCustomers,
                    borderColor: '#a78bfa',
                    backgroundColor: 'rgba(167, 139, 250, 0.1)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Top Products Chart
    const productsCtx = document.getElementById('productsChart').getContext('2d');
    new Chart(productsCtx, {
        type: 'doughnut',
        data: {
            labels: analyticsData.topProducts,
            datasets: [{
                data: analyticsData.productSales,
                backgroundColor: [
                    '#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 16
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const value = context.raw;
                            const percentage = Math.round((value / total) * 100);
                            return `${context.label}: ${value} sales (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });

    // Sales Funnel Chart
    const funnelCtx = document.getElementById('funnelChart').getContext('2d');
    new Chart(funnelCtx, {
        type: 'bar',
        data: {
            labels: analyticsData.funnelStages,
            datasets: [{
                data: analyticsData.funnelData,
                backgroundColor: [
                    '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7'
                ],
                borderRadius: 4,
                barPercentage: 0.5
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const percentage = Math.round((context.raw / analyticsData.funnelData[0]) * 100);
                            return `${context.raw} customers (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                x: { beginAtZero: true }
            }
        }
    });

    // Update summary cards with sample data
    document.getElementById('net-profit').textContent = 'KSh ' + (analyticsData.profits.slice(-1)[0] - analyticsData.losses.slice(-1)[0]).toLocaleString();
    document.getElementById('new-customers').textContent = analyticsData.newCustomers.slice(-1)[0];
    document.getElementById('returning-rate').textContent = Math.round(
        (analyticsData.returningCustomers.slice(-1)[0] / 
        (analyticsData.newCustomers.slice(-1)[0] + analyticsData.returningCustomers.slice(-1)[0])) * 100
    ) + '%';

    // Time period selector functionality
    document.getElementById('analytics-period').addEventListener('change', function() {
        // In a real app, this would fetch new data from your backend
        console.log('Time period changed to:', this.value);
        // You would then update all charts with new data
    });
});
// Dropdown functionality
document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
    toggle.addEventListener('click', function(e) {
        e.preventDefault();
        const dropdown = this.parentElement;
        dropdown.classList.toggle('active');
        
        // Close other open dropdowns
        document.querySelectorAll('.dropdown').forEach(item => {
            if (item !== dropdown) {
                item.classList.remove('active');
            }
        });
    });
});

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown').forEach(item => {
            item.classList.remove('active');
        });
    }
});