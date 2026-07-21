import React, { useEffect, useState } from 'react';
import {
  Button,
  Form,
  Input,
  message,
  Modal,
  Space,
  Table,
  Tag,
  Row,
  Col,
  InputNumber,
  DatePicker
} from 'antd';

import {
  SearchOutlined,
  PlusOutlined,
  MinusCircleOutlined,
  DownloadOutlined,
  ReloadOutlined
} from '@ant-design/icons';

import dayjs from 'dayjs';
import { OrderService } from '../../services/orderService';
import { exportFile } from '../../utils/exportFile';
import { isPermissionAction } from '../../utils/helper';

const { RangePicker } = DatePicker;

function OrderPage() {
  const [form] = Form.useForm();

  const [state, setState] = useState({
    list: [],
    total: 0,
    loading: false,
    open: false
  });

  const [filter, setFilter] = useState({
    txt_search: '',
    date_range: null,
    page: 1,
    limit: 15
  });

  // ========================
  // GET LIST
  // ========================
  const getList = async () => {
    setState(prev => ({ ...prev, loading: true }));

    try {
      const params = {
        order_no: filter.txt_search || null,
        start_date: filter.date_range?.[0]?.format('YYYY-MM-DD') || null,
        end_date: filter.date_range?.[1]?.format('YYYY-MM-DD') || null,
        page: filter.page,
        limit: filter.limit
      };

      const res = await OrderService.getList(params);

      setState(prev => ({
        ...prev,
        list: res?.list?.data ?? [],
        total: res?.total ?? 0,
        loading: false
      }));
    } catch (err) {
      console.log(err);
      setState(prev => ({ ...prev, loading: false }));
      message.error('Failed to load orders');
    }
  };

  useEffect(() => {
    getList();
  }, [filter.page]);

  // ========================
  // EXPORT
  // ========================
  const handleExport = () => {
    exportFile(state.list, 'Orders_Report');
  };

  // ========================
  // DELETE
  // ========================
  const handleDelete = (data) => {
    Modal.confirm({
      title: 'Confirm Delete',
      content: `Delete order ${data.order_no}?`,
      onOk: async () => {
        await OrderService.delete(data.id);
        message.success('Deleted successfully');
        getList();
      }
    });
  };

  // ========================
  // TABLE COLUMNS
  // ========================
  const columns = [
    {
      title: 'Order ID',
      dataIndex: 'order_no',
      render: val => <Tag color="blue">{val}</Tag>
    },
    {
      title: 'Customer',
      render: (_, r) => r.customer?.name || '-'
    },
    {
      title: 'Grand Total',
      dataIndex: 'grand_total',
      render: v => `$${Number(v || 0).toFixed(2)}`
    },
    {
      title: 'Status',
      dataIndex: 'order_status',
      render: v => (
        <Tag color={v === 'completed' ? 'green' : 'orange'}>
          {v}
        </Tag>
      )
    },
    {
      title: 'Created',
      render: (_, r) =>
        r.created_at
          ? dayjs(r.created_at).format('DD MMM YYYY, hh:mm A')
          : '-'
    },
    {
      title: 'Action',
      render: (_, data) => (
        <Space>
          {isPermissionAction('orders.delete') && (
            <Button danger onClick={() => handleDelete(data)}>
              Delete
            </Button>
          )}
        </Space>
      )
    }
  ];

  // ========================
  // UI
  // ========================
  return (
    <div style={{ padding: 20 }}>

      {/* FILTER BAR */}
      <Row justify="space-between" style={{ marginBottom: 15 }}>
        <Space>
          <Input
            placeholder="Search Order No..."
            prefix={<SearchOutlined />}
            value={filter.txt_search}
            onChange={e =>
              setFilter({ ...filter, txt_search: e.target.value })
            }
          />

          <RangePicker
            onChange={val =>
              setFilter({ ...filter, date_range: val })
            }
          />

          <Button type="primary" onClick={getList}>
            Search
          </Button>

          <Button icon={<ReloadOutlined />} onClick={getList} />
        </Space>

        <Space>
          {isPermissionAction('orders.export') && (
            <Button
              icon={<DownloadOutlined />}
              onClick={handleExport}
            >
              Export
            </Button>
          )}

          {isPermissionAction('orders.create') && (
            <Button
              type="primary"
              icon={<PlusOutlined />}
              onClick={() =>
                setState(p => ({ ...p, open: true }))
              }
            >
              New Order
            </Button>
          )}
        </Space>
      </Row>

      {/* TABLE */}
      <Table
        dataSource={state.list}
        columns={columns}
        rowKey="id"
        loading={state.loading}
        pagination={{
          total: state.total,
          pageSize: filter.limit,
          onChange: page =>
            setFilter(f => ({ ...f, page }))
        }}
      />

      {/* MODAL */}
      <Modal
        open={state.open}
        onCancel={() =>
          setState(p => ({ ...p, open: false }))
        }
        footer={null}
        width={800}
        title="Add New Order"
      >
        <Form
          form={form}
          layout="vertical"
          onFinish={async values => {
            try {
              await OrderService.create(values);
              message.success('Order Created!');
              setState(p => ({ ...p, open: false }));
              form.resetFields();
              getList();
            } catch (err) {
              message.error('Create failed');
            }
          }}
        >
          <Form.List name="items">
            {(fields, { add, remove }) => (
              <>
                {fields.map(({ key, name }) => (
                  <Row gutter={10} key={key}>
                    <Col span={8}>
                      <Form.Item
                        name={[name, 'product_id']}
                        rules={[{ required: true }]}
                      >
                        <Input placeholder="Product ID" />
                      </Form.Item>
                    </Col>

                    <Col span={6}>
                      <Form.Item
                        name={[name, 'quantity']}
                        rules={[{ required: true }]}
                      >
                        <InputNumber
                          style={{ width: '100%' }}
                          placeholder="Qty"
                        />
                      </Form.Item>
                    </Col>

                    <Col span={6}>
                      <Form.Item
                        name={[name, 'unit_price']}
                        rules={[{ required: true }]}
                      >
                        <InputNumber
                          style={{ width: '100%' }}
                          placeholder="Price"
                        />
                      </Form.Item>
                    </Col>

                    <Col span={2}>
                      <MinusCircleOutlined
                        onClick={() => remove(name)}
                      />
                    </Col>
                  </Row>
                ))}

                <Button
                  type="dashed"
                  onClick={() => add()}
                  block
                  icon={<PlusOutlined />}
                >
                  Add Product
                </Button>
              </>
            )}
          </Form.List>

          <Button
            type="primary"
            htmlType="submit"
            style={{ marginTop: 15 }}
          >
            Submit Order
          </Button>
        </Form>
      </Modal>
    </div>
  );
}

export default OrderPage;