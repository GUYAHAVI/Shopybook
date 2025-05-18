
@extends('layouts.dash')
@section('title', 'Shopybook Dashboard')
@section('content')
    <!-- MAIN -->
    <main>
        <div class="head-title">
            <div class="left">
                <h1>Business Dashboard</h1>
                <ul class="breadcrumb">
                    <li>
                        <a href="#">Shopybook</a>
                    </li>
                    <li><i class='bx bx-chevron-right'></i></li>
                    <li>
                        <a class="active" href="#">Dashboard</a>
                    </li>
                </ul>
            </div>
            <a href="#" class="btn-download">
                <i class='bx bxs-file-pdf bx-fade-down-hover'></i>
                <span class="text">Export Report</span>
            </a>
        </div>

        <ul class="box-info">
            <li>
                <i class='bx bxs-cart-alt'></i>
                <span class="text">
                    <h3 id="today-orders">0</h3>
                    <p>Today's Orders</p>
                </span>
            </li>
            <li>
                <i class='bx bxs-coin-stack'></i>
                <span class="text">
                    <h3 id="today-sales">KSh 0.00</h3>
                    <p>Today's Sales</p>
                </span>
            </li>
            <li>
                <i class='bx bxs-chart'></i>
                <span class="text">
                    <h3 id="conversion-rate">0%</h3>
                    <p>Conversion Rate</p>
                </span>
            </li>
        </ul>

     
        <div class="head-title">
            <div class="left">
                <h1>Business Analytics</h1>
                <ul class="breadcrumb">
                    <li><a href="#">Dashboard</a></li>
                    <li><i class='bx bx-chevron-right'></i></li>
                    <li><a class="active" href="#">Analytics</a></li>
                </ul>
            </div>
            <div class="time-period-selector">
                <select id="analytics-period" class="form-control">
                    <option value="7">Last 7 Days</option>
                    <option value="30" selected>Last 30 Days</option>
                    <option value="90">Last Quarter</option>
                    <option value="365">Last Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
        </div>

        <!-- Profit/Loss Summary Cards -->
        <ul class="box-info">
            <li>
                <i class='bx bxs-wallet'></i>
                <span class="text">
                    <h3 id="net-profit">KSh 0.00</h3>
                    <p>Net Profit</p>
                    <span class="trend up"><i class='bx bxs-up-arrow'></i> 12%</span>
                </span>
            </li>
            <li>
                <i class='bx bxs-group'></i>
                <span class="text">
                    <h3 id="new-customers">0</h3>
                    <p>New Customers</p>
                    <span class="trend down"><i class='bx bxs-down-arrow'></i> 5%</span>
                </span>
            </li>
            <li>
                <i class='bx bxs-repeat'></i>
                <span class="text">
                    <h3 id="returning-rate">0%</h3>
                    <p>Returning Rate</p>
                    <span class="trend up"><i class='bx bxs-up-arrow'></i> 8%</span>
                </span>
            </li>
        </ul>

        <!-- Main Charts Section -->
        <div class="chart-row" style="margin-top: 20px;">
            <!-- Profit/Loss Trend Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Profit & Loss Trend</h3>
                    <div class="chart-legend">
                        <span class="legend-profit"><i class='bx bxs-square'></i> Profit</span>
                        <span class="legend-loss"><i class='bx bxs-square'></i> Loss</span>
                    </div>
                </div>
                <canvas id="profitLossChart"></canvas>
            </div>
            
            <!-- Customer Acquisition Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Customer Acquisition</h3>
                    <div class="chart-legend">
                        <span class="legend-new"><i class='bx bxs-square'></i> New</span>
                        <span class="legend-returning"><i class='bx bxs-square'></i> Returning</span>
                    </div>
                </div>
                <canvas id="customerChart"></canvas>
            </div>
        </div>

        <!-- Secondary Charts Row -->
        <div class="chart-row">
            <!-- Top Products Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Top Selling Products</h3>
                    <a href="#" class="view-all">View All</a>
                </div>
                <canvas id="productsChart"></canvas>
            </div>
            
            <!-- Sales Funnel Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3>Sales Conversion Funnel</h3>
                </div>
                <canvas id="funnelChart"></canvas>
            </div>
        </div>

        <!-- AI Recommendations Section -->
        <div class="ai-recommendations">
            <div class="section-header">
                <h3><i class='bx bxs-bot'></i> AI Business Recommendations</h3>
            </div>
            <div class="recommendation-cards">
                <div class="recommendation-card">
                    <div class="rec-icon"><i class='bx bxs-zap'></i></div>
                    <h4>Boost Product Visibility</h4>
                    <p>Your "Handmade Soaps" have high conversion but low views. Consider promoting them on social media.</p>
                    <div class="rec-actions">
                        <button class="btn-sm btn-primary">Create Promotion</button>
                        <button class="btn-sm btn-outline">Dismiss</button>
                    </div>
                </div>
                <div class="recommendation-card">
                    <div class="rec-icon"><i class='bx bxs-time-five'></i></div>
                    <h4>Best Time to Post</h4>
                    <p>Your customers are most active between 7-9pm. Schedule posts and promotions during this window.</p>
                    <div class="rec-actions">
                        <button class="btn-sm btn-primary">Set Reminder</button>
                    </div>
                </div>
                <div class="recommendation-card">
                    <div class="rec-icon"><i class='bx bxs-package'></i></div>
                    <h4>Product Suggestion</h4>
                    <p>Based on your soap sales, consider adding complementary products like loofahs or bath salts.</p>
                    <div class="rec-actions">
                        <button class="btn-sm btn-primary">View Suppliers</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Trends Table -->
        <div class="table-data">
            <div class="order">
                <div class="head">
                    <h3>Customer Purchase Trends</h3>
                    <i class='bx bx-filter'></i>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Last Purchase</th>
                            <th>Frequency</th>
                            <th>Avg. Spend</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <img src="https://placehold.co/600x400/png">
                                <p>James Mwangi</p>
                            </td>
                            <td>2 days ago</td>
                            <td>Weekly</td>
                            <td>KSh 1,200</td>
                            <td><span class="trend up"><i class='bx bxs-up-arrow'></i> 15%</span></td>
                        </tr>
                        <tr>
                            <td>
                                <img src="https://placehold.co/600x400/png">
                                <p>Sarah Kamau</td>
                            <td>1 week ago</td>
                            <td>Monthly</td>
                            <td>KSh 2,500</td>
                            <td><span class="trend stable"><i class='bx bxs-right-arrow'></i> 2%</span></td>
                        </tr>
                        <tr>
                            <td>
                                <img src="https://placehold.co/600x400/png">
                                <p>David Ochieng</td>
                            <td>3 weeks ago</td>
                            <td>Quarterly</td>
                            <td>KSh 3,800</td>
                            <td><span class="trend down"><i class='bx bxs-down-arrow'></i> 10%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Quick Actions Todo List -->
            <div class="todo">
                <div class="head">
                    <h3>Quick Actions</h3>
                    <i class='bx bx-plus icon'></i>
                </div>
                <ul class="todo-list">
                    <li class="not-completed">
                        <p>Contact top 3 customers with personalized offers</p>
                        <i class='bx bx-dots-vertical-rounded'></i>
                    </li>
                    <li class="completed">
                        <p>Reorder best-selling products</p>
                        <i class='bx bx-dots-vertical-rounded'></i>
                    </li>
                    <li class="not-completed">
                        <p>Analyze weekend sales spike</p>
                        <i class='bx bx-dots-vertical-rounded'></i>
                    </li>
                    <li class="not-completed">
                        <p>Setup email campaign for returning customers</p>
                        <i class='bx bx-dots-vertical-rounded'></i>
                    </li>
                </ul>
            </div>
        </div>
    
   
    </main>
    <!-- MAIN -->
@endsection
