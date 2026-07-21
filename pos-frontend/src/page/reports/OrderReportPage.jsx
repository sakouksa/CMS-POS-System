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
  Select,
  Input,
  Spin
} from 'antd';
import {
  FileTextOutlined,
  SearchOutlined,
  CheckCircleOutlined,
  ClockCircleOutlined,
  CloseCircleOutlined,
  ShoppingOutlined
} from '@ant-design/icons';
import dayjs from 'dayjs';
import { request } from '../../utils/request';
import { formatPrice, dateClient } from '../../utils/helper';

const { Title, Text } = Typography;
const { RangePicker } = DatePicker;

const OrderReportPage = () => {
  const [loading, setLoading] = useState(false);
  const [dates, setDates] = useState([
    dayjs().subtract(30, 'day'),
    dayjs()
  ]);
  const [statusFilter, setStatusFilter] = useState('');
  const [search, setSearch] = useState('');

  const [data, setData] = useState({
    summary: {
      total_count: 0,
      completed_count: 0,
      pending_count: 0,
      cancelled_count: 0,
      total_amount: 0
    },
    list: []
  });

  useEffect(() => {
    fetchOrdersReport();
  }, []);

  const fetchOrdersReport = async () => {
    setLoading(true);
    const fromDate = dates[0] ? dates[0].format('YYYY-MM-DD') : '';
    const toDate = dates[1] ? dates[1].format('YYYY-MM-DD') : '';

    let query = `report/orders?from_date=${fromDate}&to_date=${toDate}`;
    if (statusFilter) query += `&order_status=${statusFilter}`;
    if (search) query += `&txt_search=${encodeURIComponent(search)}`;

    const res = await request(query, 'get');
    if (res && !res.errors) {
      setData({
        summary: res.summary || {},
        list: res.list || []
      });
    }
    setLoading(false);
  };

  const statCards = [
    {
      title: 'TOTAL ORDERS',
      value: data.summary.total_count,
      icon: <ShoppingOutlined className='text-xl text-indigo-600' />,
      bgColor: '#e0e7ff'
    },
    {
      title: 'COMPLETED',
      value: data.summary.completed_count,
      icon: <CheckCircleOutlined className='text-xl text-emerald-600' />,
      bgColor: '#ecfdf5'
    },
    {
      title: 'PENDING',
      value: data.summary.pending_count,
      icon: <ClockCircleOutlined className='text-xl text-amber-600' />,
      bgColor: '#fef3c7'
    },
    {
      title: 'CANCELLED',
      value: data.summary.cancelled_count,
      icon: <CloseCircleOutlined className='text-xl text-red-600' />,
      bgColor: '#fee2e2'
    }
  ];

  const columns = [
    {
      title: 'Order No',
      dataIndex: 'order_no',
      key: 'order_no',
      render: text => <span className='font-mono font-bold text-slate-800'>{text}</span>
    },
    {
      title: 'Customer',
      key: 'customer',
      render: record => (
        <span>
          {record.customer
            ? `${record.customer.first_name || ''} ${record.customer.last_name || ''}`.trim() || record.customer.name
            : 'General Customer'}
        </span>
      )
    },
    {
      title: 'Payment Method',
      dataIndex: ['payment_method', 'name'],
      key: 'payment_method',
      render: text => text || <Tag color='blue'>Cash</Tag>
    },
    {
      title: 'Grand Total',
      dataIndex: 'grand_total',
      key: 'grand_total',
      align: 'right',
      render: val => <span className='font-extrabold text-slate-800'>{formatPrice(val)}</span>
    },
    {
      title: 'Status',
      dataIndex: 'order_status',
      key: 'order_status',
      align: 'center',
      render: status => {
        let color = 'green';
        if (status === 'pending') color = 'orange';
        if (status === 'cancelled') color = 'red';
        return (
          <Tag color={color} className='uppercase font-semibold rounded-full px-3 py-0.5'>
            {status}
          </Tag>
        );
      }
    },
    {
      title: 'Date',
      dataIndex: 'created_at',
      key: 'created_at',
      render: date => dateClient(date)
    }
  ];

  return (
    <div className='space-y-6'>
      {/* Header Banner */}
      <div className='bg-gradient-to-r from-blue-900 to-indigo-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4'>
        <div>
          <h1 className='text-2xl font-bold tracking-tight text-white m-0 flex items-center gap-3'>
            <FileTextOutlined className='text-blue-400' /> Order History & Status Report
          </h1>
          <p className='text-blue-100 text-sm mt-1 mb-0'>
            Track store order logs, customer transactions, order status breakdowns, and date ranges.
          </p>
        </div>
      </div>

      <Spin spinning={loading}>
        {/* Stat Cards */}
        <Row gutter={[20, 20]} className='mb-6'>
          {statCards.map((item, idx) => (
            <Col xs={24} sm={12} lg={6} key={idx}>
              <Card className='rounded-2xl border-0 shadow-sm' styles={{ body: { padding: '24px' } }}>
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

        {/* Filter Controls */}
        <Card className='rounded-xl shadow-sm border-slate-100 mb-6'>
          <Row gutter={[16, 16]} align='middle'>
            <Col xs={24} sm={8} md={8}>
              <Input
                placeholder='Search Order No...'
                prefix={<SearchOutlined className='text-slate-400' />}
                allowClear
                value={search}
                onChange={e => setSearch(e.target.value)}
                onPressEnter={fetchOrdersReport}
              />
            </Col>
            <Col xs={12} sm={6} md={6}>
              <Select
                placeholder='Order Status'
                className='w-full'
                allowClear
                value={statusFilter}
                onChange={val => setStatusFilter(val)}
                options={[
                  { label: 'Completed', value: 'completed' },
                  { label: 'Pending', value: 'pending' },
                  { label: 'Cancelled', value: 'cancelled' }
                ]}
              />
            </Col>
            <Col xs={24} sm={10} md={10} className='flex items-center gap-2 justify-end'>
              <RangePicker value={dates} onChange={val => setDates(val)} allowClear={false} />
              <Button type='primary' icon={<SearchOutlined />} onClick={fetchOrdersReport}>
                Filter
              </Button>
            </Col>
          </Row>
        </Card>

        {/* Table */}
        <Card className='rounded-2xl border-0 shadow-sm overflow-hidden'>
          <Table
            dataSource={data.list}
            columns={columns}
            rowKey='id'
            pagination={{ pageSize: 10, showTotal: total => `Total ${total} orders` }}
          />
        </Card>
      </Spin>
    </div>
  );
};

export default OrderReportPage;
