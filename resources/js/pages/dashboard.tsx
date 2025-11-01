import React, { useState, useEffect } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';
import { Area, AreaChart, CartesianGrid, XAxis, YAxis } from 'recharts';
import {
    Card,
    CardAction,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    ChartConfig,
    ChartContainer,
    ChartTooltip,
    ChartTooltipContent,
} from '@/components/ui/chart';
import {
    ToggleGroup,
    ToggleGroupItem,
} from '@/components/ui/toggle-group';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard Doanh Thu', href: '#' }];

const chartConfig = {
    revenue: {
        label: 'Doanh thu',
        theme: {
            light: 'hsl(var(--chart-1))',
            dark: '#ffffff',
        },
    },
    profit: {
        label: 'Lợi nhuận',
        theme: {
            light: 'hsl(var(--chart-2))',
            dark: '#A1A1A1',
        },
    },
} satisfies ChartConfig;

const Dashboard = () => {
    const [timeRange, setTimeRange] = useState('month');
    const [chartData, setChartData] = useState([]);
    const [loading, setLoading] = useState(false);

    // Fetch dữ liệu từ API
    const fetchRevenueData = async (type) => {
        setLoading(true);
        try {
            const response = await axios.get('/thong-ke/doanh-thu', {
                params: { type },
            });

            const data = response.data;

            // Format dữ liệu cho Recharts
            const formattedData = data.map(item => {
                let label = '';
                if (type === 'day') {
                    const date = new Date(item.thoi_gian);
                    label = `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}`;
                } else if (type === 'quarter') {
                    const [year, quarter] = item.thoi_gian.split('-');
                    label = `Q${quarter.replace('Q', '')}/${year}`;
                } else if (type === 'month') {
                    const [year, month] = item.thoi_gian.split('-');
                    label = `T${parseInt(month)}/${year}`;
                } else {
                    label = item.thoi_gian;
                }

                return {
                    date: label,
                    revenue: parseFloat(item.tong_doanh_thu),
                    profit: parseFloat(item.loi_nhuan),
                };
            });

            setChartData(formattedData);
        } catch (error) {
            console.error('Lỗi khi tải dữ liệu:', error);
            setChartData([]);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchRevenueData(timeRange);
    }, [timeRange]);

    // Tính toán thống kê
    const getCurrentStats = () => {
        if (chartData.length === 0) {
            return {
                totalRevenue: 0,
                avgRevenue: 0,
                maxRevenue: 0,
                totalProfit: 0,
                growth: 0
            };
        }

        const totalRevenue = chartData.reduce((sum, item) => sum + item.revenue, 0);
        const totalProfit = chartData.reduce((sum, item) => sum + item.profit, 0);
        const avgRevenue = totalRevenue / chartData.length;
        const maxRevenue = Math.max(...chartData.map(item => item.revenue));
        const lastValue = chartData[chartData.length - 1]?.revenue || 0;
        const prevValue = chartData[chartData.length - 2]?.revenue || 0;
        const growth = prevValue ? (((lastValue - prevValue) / prevValue) * 100).toFixed(1) : 0;

        return { totalRevenue, avgRevenue, maxRevenue, totalProfit, growth };
    };

    const stats = getCurrentStats();

    // Format tiền tệ
    const formatCurrency = (value) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(value);
    };

    // Format số ngắn gọn cho trục Y
    const formatYAxis = (value) => {
        if (value >= 1000000000) {
            return `${(value / 1000000000).toFixed(1)}B`;
        } else if (value >= 1000000) {
            return `${(value / 1000000).toFixed(0)}M`;
        }
        return value;
    };

    const getTimeRangeLabel = () => {
        switch (timeRange) {
            case 'day': return 'Hàng Ngày';
            case 'month': return 'Hàng Tháng';
            case 'quarter': return 'Hàng Quý';
            case 'year': return 'Hàng Năm';
            default: return '';
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard Doanh Thu" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
                {/* Statistics Cards */}
                <div className="grid auto-rows-min gap-4 md:grid-cols-4">
                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Tổng Doanh Thu</CardDescription>
                            <CardTitle className="text-3xl">{formatCurrency(stats.totalRevenue)}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-xs text-muted-foreground">
                                {chartData.length} {timeRange === 'day' ? 'ngày' : timeRange === 'month' ? 'tháng' : timeRange === 'quarter' ? 'quý' : 'năm'}
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Trung Bình/Kỳ</CardDescription>
                            <CardTitle className="text-3xl">{formatCurrency(stats.avgRevenue)}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-xs text-muted-foreground">
                                Doanh thu trung bình
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Tổng Lợi Nhuận</CardDescription>
                            <CardTitle className="text-3xl text-green-600">{formatCurrency(stats.totalProfit)}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-xs text-muted-foreground">
                                Lợi nhuận gộp
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader className="pb-2">
                            <CardDescription>Tăng Trưởng</CardDescription>
                            <CardTitle className={`text-3xl ${stats.growth >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                                {stats.growth >= 0 ? '+' : ''}{stats.growth}%
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-xs text-muted-foreground">
                                So với kỳ trước
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Chart */}
                <Card className="@container/card">
                    <CardHeader>
                        <CardTitle>Biểu Đồ Doanh Thu & Lợi Nhuận</CardTitle>
                        <CardDescription>
                            {getTimeRangeLabel()}
                        </CardDescription>
                        <CardAction>
                            <ToggleGroup
                                type="single"
                                value={timeRange}
                                onValueChange={setTimeRange}
                                variant="outline"
                                className="hidden *:data-[slot=toggle-group-item]:!px-4 @[767px]/card:flex"
                            >
                                <ToggleGroupItem value="day" disabled={loading}>Theo Ngày</ToggleGroupItem>
                                <ToggleGroupItem value="month" disabled={loading}>Theo Tháng</ToggleGroupItem>
                                <ToggleGroupItem value="quarter" disabled={loading}>Theo Quý</ToggleGroupItem>
                                <ToggleGroupItem value="year" disabled={loading}>Theo Năm</ToggleGroupItem>
                            </ToggleGroup>
                            <Select value={timeRange} onValueChange={setTimeRange}>
                                <SelectTrigger
                                    className="flex w-40 **:data-[slot=select-value]:block **:data-[slot=select-value]:truncate @[767px]/card:hidden"
                                    size="sm"
                                    aria-label="Chọn khoảng thời gian"
                                >
                                    <SelectValue placeholder="Theo Tháng" />
                                </SelectTrigger>
                                <SelectContent className="rounded-xl">
                                    <SelectItem value="day" className="rounded-lg">Theo Ngày</SelectItem>
                                    <SelectItem value="month" className="rounded-lg">Theo Tháng</SelectItem>
                                    <SelectItem value="quarter" className="rounded-lg">Theo Quý</SelectItem>
                                    <SelectItem value="year" className="rounded-lg">Theo Năm</SelectItem>
                                </SelectContent>
                            </Select>
                        </CardAction>
                    </CardHeader>
                    <CardContent className="px-2 pt-4 sm:px-6 sm:pt-6">
                        {loading ? (
                            <div className="flex h-[350px] items-center justify-center">
                                <div className="flex items-center gap-2">
                                    <div className="h-6 w-6 animate-spin rounded-full border-4 border-primary border-t-transparent"></div>
                                    <span className="text-muted-foreground">Đang tải dữ liệu...</span>
                                </div>
                            </div>
                        ) : chartData.length > 0 ? (
                            <ChartContainer
                                config={chartConfig}
                                className="aspect-auto h-[350px] w-full"
                            >
                                <AreaChart data={chartData}>
                                    <defs>
                                        <linearGradient id="fillRevenue" x1="0" y1="0" x2="0" y2="1">
                                            <stop
                                                offset="5%"
                                                stopColor="var(--color-revenue)"
                                                stopOpacity={0.8}
                                            />
                                            <stop
                                                offset="95%"
                                                stopColor="var(--color-revenue)"
                                                stopOpacity={0.1}
                                            />
                                        </linearGradient>
                                        <linearGradient id="fillProfit" x1="0" y1="0" x2="0" y2="1">
                                            <stop
                                                offset="5%"
                                                stopColor="var(--color-profit)"
                                                stopOpacity={0.8}
                                            />
                                            <stop
                                                offset="95%"
                                                stopColor="var(--color-profit)"
                                                stopOpacity={0.1}
                                            />
                                        </linearGradient>
                                    </defs>
                                    <CartesianGrid vertical={false} />
                                    <XAxis
                                        dataKey="date"
                                        tickLine={false}
                                        axisLine={false}
                                        tickMargin={8}
                                        minTickGap={32}
                                    />
                                    <YAxis
                                        tickLine={false}
                                        axisLine={false}
                                        tickMargin={8}
                                        tickFormatter={formatYAxis}
                                    />
                                    <ChartTooltip
                                        cursor={false}
                                        content={
                                            <ChartTooltipContent
                                                indicator="dot"
                                                labelFormatter={(value) => `${value}`}
                                                formatter={(value) => formatCurrency(value)}
                                            />
                                        }
                                    />
                                    <Area
                                        dataKey="profit"
                                        type="natural"
                                        fill="url(#fillProfit)"
                                        stroke="var(--color-profit)"
                                        stackId="a"
                                    />
                                    <Area
                                        dataKey="revenue"
                                        type="natural"
                                        fill="url(#fillRevenue)"
                                        stroke="var(--color-revenue)"
                                        stackId="a"
                                    />
                                </AreaChart>
                            </ChartContainer>
                        ) : (
                            <div className="flex h-[350px] items-center justify-center text-muted-foreground">
                                Không có dữ liệu
                            </div>
                        )}
                    </CardContent>
                </Card>

            </div>
        </AppLayout>
    );
};

export default Dashboard;
