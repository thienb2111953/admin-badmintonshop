import React, { useState, useEffect, useRef } from 'react';
import { Chart, registerables } from 'chart.js';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard Doanh Thu', href: '#' }];

Chart.register(...registerables);

const Dashboard = () => {
  const [timeRange, setTimeRange] = useState('month');
  const [revenueData, setRevenueData] = useState({ labels: [], data: [] });
  const [loading, setLoading] = useState(false);
  const chartRef = useRef(null);
  const chartInstance = useRef(null);

  // Fetch dữ liệu từ API
  const fetchRevenueData = async (type) => {
    setLoading(true);
    try {
      const response = await axios.get('/thong-ke/doanh-thu', {
        params: { type }
      });

      const data = response.data;
      
      // Format labels theo từng loại
      const formattedLabels = data.map(item => {
        if (type === 'day') {
          // Format: 2024-10-01 -> 01/10
          const date = new Date(item.thoi_gian);
          return `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}`;
        } else if (type === 'month') {
          // Format: 2024-10 -> Tháng 10/2024
          const [year, month] = item.thoi_gian.split('-');
          return `Tháng ${parseInt(month)}/${year}`;
        } else {
          // Format: 2024 -> 2024
          return item.thoi_gian;
        }
      });

      const formattedData = data.map(item => parseFloat(item.tong_doanh_thu));

      setRevenueData({
        labels: formattedLabels,
        data: formattedData
      });
    } catch (error) {
      console.error('Lỗi khi tải dữ liệu:', error);
      // Nếu lỗi, hiển thị dữ liệu trống
      setRevenueData({ labels: [], data: [] });
    } finally {
      setLoading(false);
    }
  };

  // Load dữ liệu khi component mount hoặc khi thay đổi timeRange
  useEffect(() => {
    fetchRevenueData(timeRange);
  }, [timeRange]);

  // Tính toán thống kê
  const getCurrentStats = () => {
    if (revenueData.data.length === 0) {
      return { total: 0, avg: 0, max: 0, min: 0, growth: 0 };
    }

    const total = revenueData.data.reduce((sum, val) => sum + val, 0);
    const avg = total / revenueData.data.length;
    const max = Math.max(...revenueData.data);
    const min = Math.min(...revenueData.data);
    const lastValue = revenueData.data[revenueData.data.length - 1];
    const prevValue = revenueData.data[revenueData.data.length - 2];
    const growth = prevValue ? (((lastValue - prevValue) / prevValue) * 100).toFixed(1) : 0;

    return { total, avg, max, min, growth };
  };

  const stats = getCurrentStats();

  // Format tiền tệ
  const formatCurrency = (value) => {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND',
    }).format(value);
  };

  // Cập nhật biểu đồ
  useEffect(() => {
    if (chartRef.current && revenueData.data.length > 0) {
      const ctx = chartRef.current.getContext('2d');

      // Hủy biểu đồ cũ nếu tồn tại
      if (chartInstance.current) {
        chartInstance.current.destroy();
      }

      chartInstance.current = new Chart(ctx, {
        type: 'line',
        data: {
          labels: revenueData.labels,
          datasets: [
            {
              label: 'Doanh thu',
              data: revenueData.data,
              borderColor: 'rgb(59, 130, 246)',
              backgroundColor: 'rgba(59, 130, 246, 0.1)',
              tension: 0.4,
              fill: true,
              pointRadius: 4,
              pointHoverRadius: 6,
              pointBackgroundColor: 'rgb(59, 130, 246)',
              pointBorderColor: '#fff',
              pointBorderWidth: 2,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'top',
              labels: {
                font: {
                  size: 14,
                  family: 'Inter, system-ui, sans-serif',
                },
                padding: 15,
              },
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              titleFont: {
                size: 14,
              },
              bodyFont: {
                size: 13,
              },
              callbacks: {
                label: function (context) {
                  return 'Doanh thu: ' + formatCurrency(context.parsed.y);
                },
              },
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function (value) {
                  if (value >= 1000000000) {
                    return (value / 1000000000).toFixed(1) + 'B';
                  } else if (value >= 1000000) {
                    return (value / 1000000).toFixed(0) + 'M';
                  }
                  return value;
                },
                font: {
                  size: 12,
                },
              },
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
              },
            },
            x: {
              ticks: {
                font: {
                  size: 12,
                },
                maxRotation: 45,
                minRotation: 0,
              },
              grid: {
                display: false,
              },
            },
          },
        },
      });
    }

    return () => {
      if (chartInstance.current) {
        chartInstance.current.destroy();
      }
    };
  }, [revenueData]);

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Trang chủ" />
      <div className="min-h-screen bg-gray-50 p-6">
        <div className="mx-auto max-w-7xl">
          {/* Header */}
          <div className="mb-6">
            <h1 className="mb-2 text-3xl font-bold text-gray-900">Dashboard Doanh Thu</h1>
            <p className="text-gray-600">Theo dõi và phân tích doanh thu của bạn</p>
          </div>

          {/* Time Range Selector */}
          <div className="mb-6 flex gap-2">
            <button
              onClick={() => setTimeRange('day')}
              disabled={loading}
              className={`rounded-lg px-6 py-2 font-medium transition-colors ${
                timeRange === 'day'
                  ? 'bg-blue-500 text-white shadow-md'
                  : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-100'
              } ${loading ? 'cursor-not-allowed opacity-50' : ''}`}
            >
              Theo Ngày
            </button>
            <button
              onClick={() => setTimeRange('month')}
              disabled={loading}
              className={`rounded-lg px-6 py-2 font-medium transition-colors ${
                timeRange === 'month'
                  ? 'bg-blue-500 text-white shadow-md'
                  : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-100'
              } ${loading ? 'cursor-not-allowed opacity-50' : ''}`}
            >
              Theo Tháng
            </button>
            <button
              onClick={() => setTimeRange('year')}
              disabled={loading}
              className={`rounded-lg px-6 py-2 font-medium transition-colors ${
                timeRange === 'year'
                  ? 'bg-blue-500 text-white shadow-md'
                  : 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-100'
              } ${loading ? 'cursor-not-allowed opacity-50' : ''}`}
            >
              Theo Năm
            </button>
          </div>

          {/* Loading State */}
          {loading && (
            <div className="mb-6 rounded-lg border border-blue-200 bg-blue-50 p-4 text-center">
              <div className="inline-block h-6 w-6 animate-spin rounded-full border-4 border-blue-500 border-t-transparent"></div>
              <span className="ml-2 text-blue-700">Đang tải dữ liệu...</span>
            </div>
          )}

          {/* Statistics Cards */}
          <div className="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
            <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
              <div className="mb-2 flex items-center justify-between">
                <p className="text-sm font-medium text-gray-600">Tổng Doanh Thu</p>
                <span className="text-2xl">💰</span>
              </div>
              <p className="text-2xl font-bold text-gray-900">{formatCurrency(stats.total)}</p>
              <p className="mt-1 text-xs text-gray-500">
                {revenueData.data.length} {timeRange === 'day' ? 'ngày' : timeRange === 'month' ? 'tháng' : 'năm'}
              </p>
            </div>

            <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
              <div className="mb-2 flex items-center justify-between">
                <p className="text-sm font-medium text-gray-600">Trung Bình</p>
                <span className="text-2xl">📊</span>
              </div>
              <p className="text-2xl font-bold text-gray-900">{formatCurrency(stats.avg)}</p>
              <p className="mt-1 text-xs text-gray-500">Mỗi kỳ</p>
            </div>

            <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
              <div className="mb-2 flex items-center justify-between">
                <p className="text-sm font-medium text-gray-600">Cao Nhất</p>
                <span className="text-2xl">📈</span>
              </div>
              <p className="text-2xl font-bold text-green-600">{formatCurrency(stats.max)}</p>
              <p className="mt-1 text-xs text-gray-500">Kỷ lục</p>
            </div>

            <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
              <div className="mb-2 flex items-center justify-between">
                <p className="text-sm font-medium text-gray-600">Tăng Trưởng</p>
                <span className="text-2xl">🚀</span>
              </div>
              <p className={`text-2xl font-bold ${stats.growth >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                {stats.growth >= 0 ? '+' : ''}
                {stats.growth}%
              </p>
              <p className="mt-1 text-xs text-gray-500">So với kỳ trước</p>
            </div>
          </div>

          {/* Chart */}
          <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 className="mb-4 text-xl font-semibold text-gray-900">
              Biểu Đồ Doanh Thu{' '}
              {timeRange === 'day' ? 'Hàng Ngày' : timeRange === 'month' ? 'Hàng Tháng' : 'Hàng Năm'}
            </h2>
            {revenueData.data.length > 0 ? (
              <div style={{ height: '400px' }}>
                <canvas ref={chartRef}></canvas>
              </div>
            ) : (
              <div className="flex h-96 items-center justify-center text-gray-500">
                {loading ? 'Đang tải dữ liệu...' : 'Không có dữ liệu'}
              </div>
            )}
          </div>

          {/* Additional Info */}
          {revenueData.data.length > 0 && (
            <div className="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
              <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h3 className="mb-3 text-lg font-semibold text-gray-900">Phân Tích</h3>
                <div className="space-y-2 text-sm">
                  <div className="flex justify-between">
                    <span className="text-gray-600">Doanh thu thấp nhất:</span>
                    <span className="font-semibold">{formatCurrency(stats.min)}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Chênh lệch cao/thấp:</span>
                    <span className="font-semibold">{formatCurrency(stats.max - stats.min)}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Độ lệch chuẩn:</span>
                    <span className="font-semibold">±{(((stats.max - stats.avg) / stats.avg) * 100).toFixed(1)}%</span>
                  </div>
                </div>
              </div>

              <div className="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h3 className="mb-3 text-lg font-semibold text-gray-900">Xu Hướng</h3>
                <div className="space-y-2 text-sm">
                  <div className="flex items-center gap-2">
                    <span className="text-2xl">{stats.growth >= 5 ? '📈' : stats.growth >= 0 ? '➡️' : '📉'}</span>
                    <span className="text-gray-700">
                      {stats.growth >= 5
                        ? 'Tăng trưởng mạnh'
                        : stats.growth >= 0
                          ? 'Tăng trưởng ổn định'
                          : 'Cần cải thiện'}
                    </span>
                  </div>
                  <p className="mt-2 text-gray-600">
                    Doanh thu đang có xu hướng {stats.growth >= 0 ? 'tích cực' : 'giảm'} so với kỳ trước.
                    {stats.growth >= 5 && ' Đây là một tín hiệu rất tốt cho sự phát triển.'}
                  </p>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </AppLayout>
  );
};

export default Dashboard;