import React, { useEffect, useState } from 'react';
import {
  Card,
  Row,
  Col,
  DatePicker,
  Button,
  Table,
  Tag,
  Typography,
  Space,
  Avatar,
  Spin
} from 'antd';
import {
  DollarOutlined,
  ShoppingOutlined,
  PercentageOutlined,
  RiseOutlined,
  SearchOutlined
} from '@ant-design/icons';
import {
  ResponsiveContainer,
  AreaChart,
  Area,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip as ChartTooltip,
  Legend
} from 'recharts';
import dayjs from 'dayjs';
import { request } from '../../utils/request';
import { formatPrice, getProductImage } from '../../utils/helper';
import config from '../../utils/config';

const { Title, Text } = Typography;
const { RangePicker } = DatePicker;

const SalesReportPage = () => {
  const [loading, setLoading] = useState(false);
  const [dates, setDates] = useState([
    dayjs().subtract(30, 'day'),
    dayjs()
  ]);

  const [data, setData] = useState({
    summary: {
      total_revenue: 0,
      total_orders: 0,
      total_discount: 0,
      avg_order_value: 0
    },
    sales_by_payment: [],
    top_products: [],
    daily_trend: []
  });

  useEffect(() => {
    fetchSalesReport();
  }, []);

  const fetchSalesReport = async () => {
    setLoading(true);
    const fromDate = dates[0] ? dates[0].format('YYYY-MM-DD') : '';
    const toDate = dates[1] ? dates[1].format('YYYY-MM-DD') : '';

    const res = await request(`report/sales?from_date=${fromDate}&to_date=${toDate}`, 'get');
    if (res && !res.errors) {
      setData({
        summary: res.summary || {},
        sales_by_payment: res.sales_by_payment || [],
        top_products: res.top_products || [],
        daily_trend: res.daily_trend || []
      });
    }
    setLoading(false);
  };

  const statCards = [
    {
      title: 'TOTAL REVENUE',
      value: formatPrice(data.summary.total_revenue),
      icon: <DollarOutlined className='text-xl text-emerald-600' />,
      bgColor: '#ecfdf5',
      color: '#059669'
    },
    {
      title: 'COMPLETED ORDERS',
      value: data.summary.total_orders,
      icon: <ShoppingOutlined className='text-xl text-indigo-600' />,
      bgColor: '#e0e7ff',
      color: '#4f46e5'
    },
    {
      title: 'DISCOUNTS GIVEN',
      value: formatPrice(data.summary.total_discount),
      icon: <PercentageOutlined className='text-xl text-amber-600' />,
      bgColor: '#fef3c7',
      color: '#d97706'
    },
    {
      title: 'AVG ORDER VALUE',
      value: formatPrice(data.summary.avg_order_value),
      icon: <RiseOutlined className='text-xl text-purple-600' />,
      bgColor: '#f3e8ff',
      color: '#9333ea'
    }
  ];

  const topProductColumns = [
    {
      title: 'Rank',
      key: 'rank',
      width: 70,
      render: (_, __, index) => (
        <span className={`w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs text-white ${index === 0 ? 'bg-amber-500' : index === 1 ? 'bg-slate-400' : index === 2 ? 'bg-amber-700' : 'bg-slate-300'}`}>
          #{index + 1}
        </span>
      )
    },
    {
      title: 'Product',
      key: 'product',
      render: record => (
        <Space size='middle'>
          <Avatar
            shape='square'
            size={48}
            src={getProductImage(record.image)}
            className='rounded-lg border border-slate-100'
          />
          <div>
            <div className='font-bold text-slate-800 text-sm'>{record.product_name}</div>
            <div className='text-xs text-slate-400'>Qty Sold: {record.total_qty} units</div>
          </div>
        </Space>
      )
    },
    {
      title: 'Total Sales',
      dataIndex: 'total_sales',
      key: 'total_sales',
      align: 'right',
      render: val => <span className='font-extrabold text-emerald-600 text-sm'>{formatPrice(val)}</span>
    }
  ];

  return (
    <div className='space-y-6'>
      {/* Header Banner */}
      <div className='bg-gradient-to-r from-emerald-900 to-teal-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4'>
        <div>
          <h1 className='text-2xl font-bold tracking-tight text-white m-0 flex items-center gap-3'>
            <RiseOutlined className='text-emerald-400' /> Sales & Revenue Report
          </h1>
          <p className='text-emerald-100 text-sm mt-1 mb-0'>
            Detailed sales performance, revenue analytics, daily trends, and top selling products.
          </p>
        </div>

        {/* Date Filter */}
        <div className='bg-white/10 backdrop-blur-md p-2 rounded-xl border border-white/20 flex flex-wrap items-center gap-2'>
          <RangePicker
            value={dates}
            onChange={val => setDates(val)}
            allowClear={false}
            className='rounded-lg border-0'
          />
          <Button
            type='primary'
            icon={<SearchOutlined />}
            onClick={fetchSalesReport}
            className='bg-emerald-500 hover:bg-emerald-400 border-none rounded-lg font-semibold'
          >
            Apply Filter
          </Button>
        </div>
      </div>

      <Spin spinning={loading}>
        {/* Stat Cards */}
        <Row gutter={[20, 20]} className='mb-6'>
          {statCards.map((item, idx) => (
            <Col xs={24} sm={12} lg={6} key={idx}>
              <Card className='rounded-2xl border-0 shadow-sm hover:shadow-md transition-all duration-300' styles={{ body: { padding: '24px' } }}>
                <div className='flex items-center justify-between'>
                  <div>
                    <Text type='secondary' className='text-xs uppercase font-bold tracking-wider text-slate-400 block mb-1'>
                      {item.title}
                    </Text>
                    <Title level={3} className='!m-0 font-extrabold text-slate-800'>
                      {item.value}
                    </Title>
                  </div>
                  <div
                    style={{ backgroundColor: item.bgColor }}
                    className='w-12 h-12 rounded-xl flex items-center justify-center shadow-inner'
                  >
                    {item.icon}
                  </div>
                </div>
              </Card>
            </Col>
          ))}
        </Row>

        {/* Daily Sales Trend Chart & Payment Methods */}
        <Row gutter={[20, 20]} className='mb-6'>
          <Col xs={24} lg={16}>
            <Card title={<span className='font-bold text-slate-800'>Daily Revenue Trend</span>} className='rounded-2xl border-0 shadow-sm h-full'>
              <div style={{ width: '100%', height: 350 }}>
                {data.daily_trend.length > 0 ? (
                  <ResponsiveContainer width='100%' height='100%' minWidth={0} minHeight={300}>
                    <AreaChart data={data.daily_trend} margin={{ top: 10, right: 10, left: -20, bottom: 0 }}>
                      <defs>
                        <linearGradient id='colorRevReport' x1='0' y1='0' x2='0' y2='1'>
                          <stop offset='5%' stopColor='#10b981' stopOpacity={0.3} />
                          <stop offset='95%' stopColor='#10b981' stopOpacity={0} />
                        </linearGradient>
                      </defs>
                      <CartesianGrid strokeDasharray='3 3' vertical={false} stroke='#f1f5f9' />
                      <XAxis dataKey='date' stroke='#94a3b8' fontSize={12} tickLine={false} />
                      <YAxis stroke='#94a3b8' fontSize={12} tickLine={false} />
                      <ChartTooltip formatter={value => formatPrice(value)} />
                      <Legend verticalAlign='top' height={36} />
                      <Area
                        type='monotone'
                        name='Revenue ($)'
                        dataKey='revenue'
                        stroke='#10b981'
                        strokeWidth={3}
                        fillOpacity={1}
                        fill='url(#colorRevReport)'
                      />
                    </AreaChart>
                  </ResponsiveContainer>
                ) : (
                  <div className='flex h-full items-center justify-center text-slate-400'>
                    No sales recorded for the selected date range.
                  </div>
                )}
              </div>
            </Card>
          </Col>

          <Col xs={24} lg={8}>
            <Card title={<span className='font-bold text-slate-800'>Sales by Payment Method</span>} className='rounded-2xl border-0 shadow-sm h-full'>
              <div className='space-y-4'>
                {data.sales_by_payment.map((pay, i) => (
                  <div key={i} className='bg-slate-50 p-4 rounded-xl border border-slate-100 flex items-center justify-between'>
                    <div>
                      <div className='font-bold text-slate-800 text-sm'>{pay.payment_method || 'Cash / General'}</div>
                      <div className='text-xs text-slate-400'>{pay.count} Transactions</div>
                    </div>
                    <span className='font-extrabold text-emerald-600 text-base'>{formatPrice(pay.total)}</span>
                  </div>
                ))}
                {data.sales_by_payment.length === 0 && (
                  <div className='text-center text-slate-400 py-8'>No payment data</div>
                )}
              </div>
            </Card>
          </Col>
        </Row>

        {/* Top 10 Products Table */}
        <Card title={<span className='font-bold text-slate-800'>Top 10 Best Selling Products</span>} className='rounded-2xl border-0 shadow-sm overflow-hidden'>
          <Table
            dataSource={data.top_products}
            columns={topProductColumns}
            rowKey={(r, i) => i}
            pagination={false}
          />
        </Card>
      </Spin>
    </div>
  );
};

export default SalesReportPage;
