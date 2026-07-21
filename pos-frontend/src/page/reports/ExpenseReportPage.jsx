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
  Progress,
  Spin
} from 'antd';
import {
  DollarOutlined,
  SearchOutlined,
  UnorderedListOutlined,
  FileTextOutlined
} from '@ant-design/icons';
import dayjs from 'dayjs';
import { request } from '../../utils/request';
import { formatPrice, dateClient } from '../../utils/helper';

const { Title, Text } = Typography;
const { RangePicker } = DatePicker;

const ExpenseReportPage = () => {
  const [loading, setLoading] = useState(false);
  const [dates, setDates] = useState([
    dayjs().subtract(30, 'day'),
    dayjs()
  ]);

  const [data, setData] = useState({
    summary: {
      total_entries: 0,
      total_amount: 0
    },
    breakdown: [],
    list: []
  });

  useEffect(() => {
    fetchExpenseReport();
  }, []);

  const fetchExpenseReport = async () => {
    setLoading(true);
    const fromDate = dates[0] ? dates[0].format('YYYY-MM-DD') : '';
    const toDate = dates[1] ? dates[1].format('YYYY-MM-DD') : '';

    const res = await request(`report/expenses?from_date=${fromDate}&to_date=${toDate}`, 'get');
    if (res && !res.errors) {
      setData({
        summary: res.summary || {},
        breakdown: res.breakdown || [],
        list: res.list || []
      });
    }
    setLoading(false);
  };

  const columns = [
    {
      title: 'Expense Name',
      dataIndex: 'name',
      key: 'name',
      render: text => <span className='font-bold text-slate-800'>{text}</span>
    },
    {
      title: 'Expense Category / Type',
      dataIndex: ['expense_type', 'name'],
      key: 'expense_type',
      render: name => <Tag color='volcano' className='font-semibold rounded-full px-3'>{name || 'General Expense'}</Tag>
    },
    {
      title: 'Amount',
      dataIndex: 'amount',
      key: 'amount',
      align: 'right',
      render: val => <span className='font-extrabold text-red-600 text-sm'>{formatPrice(val)}</span>
    },
    {
      title: 'Description',
      dataIndex: 'description',
      key: 'description',
      render: text => text || <span className='text-slate-300'>No description</span>
    },
    {
      title: 'Date',
      dataIndex: 'expense_date',
      key: 'expense_date',
      render: date => dateClient(date)
    }
  ];

  return (
    <div className='space-y-6'>
      {/* Header Banner */}
      <div className='bg-gradient-to-r from-red-900 to-rose-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4'>
        <div>
          <h1 className='text-2xl font-bold tracking-tight text-white m-0 flex items-center gap-3'>
            <DollarOutlined className='text-rose-400' /> Expense & Store Overhead Report
          </h1>
          <p className='text-rose-100 text-sm mt-1 mb-0'>
            Track store operating costs, utility expenses, salary overhead, and category breakdowns.
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
            onClick={fetchExpenseReport}
            className='bg-rose-600 hover:bg-rose-500 border-none rounded-lg font-semibold'
          >
            Apply Filter
          </Button>
        </div>
      </div>

      <Spin spinning={loading}>
        {/* Stat Cards & Breakdown */}
        <Row gutter={[20, 20]} className='mb-6'>
          <Col xs={24} sm={12} lg={8}>
            <Card className='rounded-2xl border-0 shadow-sm h-full' styles={{ body: { padding: '24px' } }}>
              <Text type='secondary' className='text-xs uppercase font-bold tracking-wider text-slate-400 block mb-1'>
                TOTAL EXPENSES COST
              </Text>
              <Title level={2} className='!m-0 font-extrabold text-red-600'>
                {formatPrice(data.summary.total_amount)}
              </Title>
              <div className='mt-4 text-xs text-slate-400'>
                Total recorded entries: <span className='font-bold text-slate-700'>{data.summary.total_entries}</span>
              </div>
            </Card>
          </Col>

          <Col xs={24} lg={16}>
            <Card title={<span className='font-bold text-slate-800'>Category Expense Breakdown</span>} className='rounded-2xl border-0 shadow-sm h-full'>
              <Row gutter={[16, 16]}>
                {data.breakdown.map((item, idx) => {
                  const percent = data.summary.total_amount > 0 ? roundPercent((item.total_amount / data.summary.total_amount) * 100) : 0;
                  return (
                    <Col xs={24} sm={12} key={idx}>
                      <div className='bg-slate-50 p-3 rounded-xl border border-slate-100'>
                        <div className='flex justify-between items-center mb-1'>
                          <span className='font-bold text-slate-700 text-xs'>{item.type_name || 'Other Expense'}</span>
                          <span className='font-extrabold text-slate-800 text-xs'>{formatPrice(item.total_amount)}</span>
                        </div>
                        <Progress percent={percent} strokeColor='#ef4444' size='small' />
                      </div>
                    </Col>
                  );
                })}
              </Row>
            </Card>
          </Col>
        </Row>

        {/* Expense List Table */}
        <Card title={<span className='font-bold text-slate-800'>Expense Logs Detail</span>} className='rounded-2xl border-0 shadow-sm overflow-hidden'>
          <Table
            dataSource={data.list}
            columns={columns}
            rowKey='id'
            pagination={{ pageSize: 10, showTotal: total => `Total ${total} expenses` }}
          />
        </Card>
      </Spin>
    </div>
  );
};

function roundPercent(val) {
  return Math.round(val * 10) / 10;
}

export default ExpenseReportPage;
