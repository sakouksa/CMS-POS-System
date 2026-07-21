import React, { useEffect, useState } from 'react'
import {
  Button,
  Form,
  Input,
  message,
  Modal,
  Select,
  Space,
  Table,
  Tag,
  Row,
  Col,
  Typography,
  InputNumber,
  DatePicker,
  Divider
} from 'antd'
import {
  FilterOutlined,
  ReloadOutlined,
  SearchOutlined,
  ExclamationCircleFilled,
  PlusOutlined,
  MinusCircleOutlined,
  EyeOutlined
} from '@ant-design/icons'
import { RiSave3Fill } from 'react-icons/ri'
import { MdDelete } from 'react-icons/md'
import { FiDownload } from 'react-icons/fi'
import dayjs from 'dayjs'

import { isPermissionAction } from '../../utils/helper'
import { PurchaseService } from '../../services/purchaseService'
import PageLoader from '../../component/common/PageLoader'
import ServerErrorPage from '../error-page/500'
import { exportFile } from '../../utils/exportFile'
import { CiEdit } from 'react-icons/ci'
const { Text, Title } = Typography

function PurchasePage () {
  const [formRef] = Form.useForm()
  const [state, setState] = useState({
    list: [],
    total: 0,
    loading: false,
    open: false,
    suppliers: [],
    payment_methods: [],
    products: [],
    viewDetail: null,
    openDetail: false
  })

  const [filter, setFilter] = useState({
    txt_search: '',
    supplier_id: null,
    payment_status: null,
    page: 1,
    limit: 15
  })
  const [isServerError, setIsServerError] = useState(false)

  useEffect(() => {
    getList()
  }, [])

  const getList = async (param_filter = {}) => {
    const currentFilter = { ...filter, ...param_filter }
    setFilter(currentFilter)
    setState(pre => ({ ...pre, loading: true }))
    setIsServerError(false)

    try {
      const res = await PurchaseService.getList(currentFilter)
      if (res && !res.errors) {
        setState(pre => ({
          ...pre,
          total: res.list?.total || 0,
          list: res.list?.data || [],
          suppliers: res.suppliers || [],
          payment_methods: res.payment_methods || [],
          products: res.products || []
        }))
      } else {
        if (res?.errors?.status === 500) setIsServerError(true)
        else message.error(res?.errors?.message || 'Something went wrong!')
      }
    } catch (error) {
      setIsServerError(true)
    } finally {
      setState(pre => ({ ...pre, loading: false }))
    }
  }

  const onFinish = async values => {
    try {
      const payload = {
        ...values,
        purchase_date: values.purchase_date
          ? values.purchase_date.format('YYYY-MM-DD')
          : null,
        items: values.items?.map(item => ({
          ...item,
          expiry_date: item.expiry_date
            ? item.expiry_date.format('YYYY-MM-DD')
            : null
        }))
      }

      let res = await PurchaseService.create(payload)

      if (res && !res.errors) {
        message.success(res.message || 'Purchase created successfully!')
        handleCloseModal()
        getList()
      } else {
        message.error(res?.errors?.message || 'Operation failed!')
      }
    } catch (error) {
      message.error('Server error occurred.')
    }
  }

  const handleDelete = async data => {
    Modal.confirm({
      title: 'Confirm Deletion',
      icon: <ExclamationCircleFilled style={{ color: '#ff4d4f' }} />,
      content: `Are you sure you want to delete purchase ${data.purchase_no}? This will adjust supplier balance.`,
      okText: 'Delete',
      okType: 'danger',
      centered: true,
      onOk: async () => {
        try {
          const res = await PurchaseService.delete(data.id)
          if (res && !res.errors) {
            message.success('Deleted successfully!')
            getList()
          } else {
            message.error(res?.errors?.message || 'Delete failed!')
          }
        } catch (error) {
          message.error('Error deleting.')
        }
      }
    })
  }

  const handleShowDetail = async id => {
    setState(pre => ({ ...pre, loading: true }))
    try {
      const res = await PurchaseService.getOne(id)
      if (res && res.data) {
        setState(pre => ({ ...pre, viewDetail: res.data, openDetail: true }))
      } else {
        message.error('Cannot load purchase details.')
      }
    } catch (e) {
      message.error('Error fetching detail.')
    } finally {
      setState(pre => ({ ...pre, loading: false }))
    }
  }

  const handleEdit = data => {
    message.warning(
      'Direct updates are disabled in backend to preserve financial integrity.'
    )
  }

  const handleOpenModal = () => setState(pre => ({ ...pre, open: true }))

  const handleCloseModal = () => {
    setState(pre => ({ ...pre, open: false }))
    formRef.resetFields()
  }

  const handleReset = () => {
    const defaultFilter = {
      txt_search: '',
      supplier_id: null,
      payment_status: null,
      page: 1,
      limit: 15
    }
    setFilter(defaultFilter)
    getList(defaultFilter)
  }
  // function Export
  const handleExport = () => {
    exportFile({
      url: 'purchase-export',
      filename: 'Purchase_List'
    })
  }
  if (isServerError) return <ServerErrorPage onRetry={() => getList()} />

  return (
    <div className='p-4'>
      {state.loading && <PageLoader />}

      {/* Header Section */}
      <div className='bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6'>
        <div className='flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6'>
          <div>
            <h2 className='text-xl font-bold text-gray-900 m-0'>
              Purchase Management
              <span className='ml-2 text-sm font-normal text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full'>
                Total: {state.total}
              </span>
            </h2>
            <Text type='secondary'>
              Manage incoming purchases and supplier accounts payable balances.
            </Text>
          </div>
          <div className='flex flex-wrap items-center gap-3 w-full md:w-auto justify-end'>
            {isPermissionAction('purchase.export') && (
              <Button icon={<FiDownload />} onClick={handleExport}>
                Export Excel
              </Button>
            )}
            {isPermissionAction('purchase.create') && (
              <Button
                type='primary'
                icon={<PlusOutlined />}
                onClick={handleOpenModal}
                className='bg-indigo-600 border-0'
              >
                Add New
              </Button>
            )}
          </div>
        </div>

        {/* Filters */}
        <div
          div
          className='border-t border-gray-100 pt-6 flex flex-wrap justify-between items-center gap-4'
        >
          <Input
            placeholder='Search Invoice or Ref...'
            value={filter.txt_search}
            onChange={e =>
              setFilter(p => ({ ...p, txt_search: e.target.value }))
            }
            onPressEnter={() => getList({ page: 1 })}
            prefix={<SearchOutlined />}
            style={{ width: 250 }}
          />
          <div div className='flex items-center gap-3'>
            <Select
              placeholder='Supplier'
              allowClear
              style={{ width: 200 }}
              value={filter.supplier_id}
              onChange={v => setFilter(p => ({ ...p, supplier_id: v }))}
              options={state.suppliers.map(s => ({
                label: s.name,
                value: s.id
              }))}
            />
            <Select
              placeholder='Payment Status'
              allowClear
              style={{ width: 150 }}
              value={filter.payment_status}
              onChange={v => setFilter(p => ({ ...p, payment_status: v }))}
            >
              <Select.Option value='paid'>Paid</Select.Option>
              <Select.Option value='partial'>Partial</Select.Option>
              <Select.Option value='due'>Due</Select.Option>
            </Select>
            <Space>
              <Button onClick={handleReset} icon={<ReloadOutlined />}>
                Reset
              </Button>
              <Button
                type='primary'
                onClick={() => getList({ page: 1 })}
                icon={<FilterOutlined />}
                className='bg-indigo-600'
              >
                Filter
              </Button>
            </Space>
          </div>
        </div>
      </div>

      {/* Table Section */}
      {isPermissionAction('purchase.view') ? (
        <Table
          dataSource={state.list}
          rowKey='id'
          pagination={{
            total: state.total,
            current: filter.page,
            pageSize: filter.limit,
            onChange: (page, pageSize) => getList({ page, limit: pageSize })
          }}
          columns={[
            {
              title: 'Purchase No',
              dataIndex: 'purchase_no',
              render: (text, row) => (
                <Space direction='vertical' size={0}>
                  <Text strong className='text-indigo-600'>
                    {text}
                  </Text>
                  <Text type='secondary' style={{ fontSize: 12 }}>
                    {row.reference_no}
                  </Text>
                </Space>
              )
            },
            { title: 'Supplier', render: (_, row) => row.supplier?.name },
            {
              title: 'Date',
              dataIndex: 'purchase_date',
              render: d => (d ? dayjs(d).format('DD-MM-YYYY') : '')
            },
            {
              title: 'Grand Total',
              dataIndex: 'grand_total',
              render: v => <Text strong>${Number(v).toFixed(2)}</Text>
            },
            {
              title: 'Paid',
              dataIndex: 'paid_amount',
              render: v => <Text type='success'>${Number(v).toFixed(2)}</Text>
            },
            {
              title: 'Due',
              dataIndex: 'due_amount',
              render: v => <Text type='danger'>${Number(v).toFixed(2)}</Text>
            },
            {
              title: 'Status',
              dataIndex: 'payment_status',
              render: status => (
                <Tag
                  color={
                    status === 'paid'
                      ? 'green'
                      : status === 'partial'
                      ? 'orange'
                      : 'red'
                  }
                >
                  {status?.toUpperCase()}
                </Tag>
              )
            },
            {
              title: 'Actions',
              align: 'center',
              render: (_, data) => (
                <Space>
                  {isPermissionAction('purchase.viewone') && (
                    <Button
                      type='text'
                      onClick={() => handleShowDetail(data.id)}
                      icon={
                        <EyeOutlined
                          style={{ fontSize: 18, color: '#13c2c2' }}
                        />
                      }
                    />
                  )}
                  {isPermissionAction('purchase.delete') && (
                    <Button
                      type='text'
                      danger
                      onClick={() => handleDelete(data)}
                      icon={<MdDelete style={{ fontSize: 18 }} />}
                    />
                  )}
                </Space>
              )
            }
          ]}
        />
      ) : (
        <div className='text-center py-10 bg-gray-50 rounded-xl border border-dashed border-gray-300'>
          <Title level={4} type='danger'>
            Access Denied
          </Title>
        </div>
      )}

      {/* Modal for Creating Purchase */}
      <Modal
        title='Add New Purchase Order'
        open={state.open}
        onCancel={handleCloseModal}
        footer={null}
        width={900}
        centered
      >
        <Form
          layout='vertical'
          form={formRef}
          onFinish={onFinish}
          initialValues={{
            purchase_date: dayjs(),
            items: [{}],
            status: 'received'
          }}
        >
          <Row gutter={16}>
            <Col span={8}>
              <Form.Item
                name='supplier_id'
                label='Supplier'
                rules={[{ required: true, message: 'Please select supplier' }]}
              >
                <Select
                  placeholder='Select Supplier'
                  options={state.suppliers.map(s => ({
                    label: s.name,
                    value: s.id
                  }))}
                />
              </Form.Item>
            </Col>
            <Col span={8}>
              <Form.Item
                name='purchase_date'
                label='Purchase Date'
                rules={[{ required: true }]}
              >
                <DatePicker className='w-full' format='DD-MM-YYYY' />
              </Form.Item>
            </Col>
            <Col span={8}>
              <Form.Item
                name='payment_method_id'
                label='Payment Method'
                rules={[{ required: true }]}
              >
                <Select
                  placeholder='Method'
                  options={state.payment_methods.map(p => ({
                    label: p.name,
                    value: p.id
                  }))}
                />
              </Form.Item>
            </Col>
          </Row>

          <Divider orientation='left' style={{ fontSize: 14 }}>
            Purchase Items
          </Divider>

          <Form.List name='items'>
            {(fields, { add, remove }) => (
              <>
                {fields.map(({ key, name, ...restField }) => (
                  <Row
                    gutter={12}
                    key={key}
                    align='middle'
                    style={{ marginBottom: 8 }}
                  >
                    <Col span={8}>
                      <Form.Item
                        {...restField}
                        name={[name, 'product_id']}
                        rules={[{ required: true, message: 'Missing product' }]}
                        style={{ marginBottom: 0 }}
                      >
                        <Select
                          placeholder='Select Product'
                          showSearch
                          optionFilterProp='label'
                          options={state.products.map(p => ({
                            label: p.product_name,
                            value: p.id
                          }))}
                        />
                      </Form.Item>
                    </Col>
                    <Col span={4}>
                      <Form.Item
                        {...restField}
                        name={[name, 'quantity']}
                        rules={[{ required: true, message: 'Qty required' }]}
                        style={{ marginBottom: 0 }}
                      >
                        <InputNumber
                          placeholder='Qty'
                          min={1}
                          className='w-full'
                        />
                      </Form.Item>
                    </Col>
                    <Col span={5}>
                      <Form.Item
                        {...restField}
                        name={[name, 'purchase_unit_cost']}
                        rules={[{ required: true, message: 'Cost required' }]}
                        style={{ marginBottom: 0 }}
                      >
                        <InputNumber
                          placeholder='Cost'
                          min={0}
                          className='w-full'
                          prefix='$'
                          step={0.01}
                        />
                      </Form.Item>
                    </Col>
                    <Col span={5}>
                      <Form.Item
                        {...restField}
                        name={[name, 'expiry_date']}
                        style={{ marginBottom: 0 }}
                      >
                        <DatePicker
                          placeholder='Expiry Date'
                          className='w-full'
                          format='DD-MM-YYYY'
                        />
                      </Form.Item>
                    </Col>
                    <Col span={2}>
                      {fields.length > 1 && (
                        <MinusCircleOutlined
                          className='text-red-500'
                          onClick={() => remove(name)}
                        />
                      )}
                    </Col>
                  </Row>
                ))}
                <Form.Item style={{ marginTop: 12 }}>
                  <Button
                    type='dashed'
                    onClick={() => add()}
                    block
                    icon={<PlusOutlined />}
                  >
                    Add Product
                  </Button>
                </Form.Item>
              </>
            )}
          </Form.List>

          <Divider />

          <Row gutter={16}>
            <Col span={6}>
              <Form.Item name='discount' label='Discount'>
                <InputNumber
                  className='w-full'
                  prefix='$'
                  min={0}
                  placeholder='0.00'
                />
              </Form.Item>
            </Col>
            <Col span={6}>
              <Form.Item name='tax' label='Tax'>
                <InputNumber
                  className='w-full'
                  prefix='$'
                  min={0}
                  placeholder='0.00'
                />
              </Form.Item>
            </Col>
            <Col span={6}>
              <Form.Item
                name='paid_amount'
                label='Paid Amount'
                rules={[{ required: true }]}
              >
                <InputNumber
                  className='w-full'
                  prefix='$'
                  min={0}
                  placeholder='0.00'
                />
              </Form.Item>
            </Col>
            <Col span={6}>
              <Form.Item
                name='status'
                label='Receive Status'
                rules={[{ required: true }]}
              >
                <Select placeholder='Status'>
                  <Select.Option value='received'>Received</Select.Option>
                  <Select.Option value='pending'>Pending</Select.Option>
                  <Select.Option value='ordered'>Ordered</Select.Option>
                </Select>
              </Form.Item>
            </Col>
          </Row>

          <div className='text-right mt-6'>
            <Space>
              <Button onClick={handleCloseModal}>Cancel</Button>
              <Button
                type='primary'
                htmlType='submit'
                icon={<RiSave3Fill />}
                className='bg-indigo-600 border-0'
              >
                Save Purchase
              </Button>
            </Space>
          </div>
        </Form>
      </Modal>

      {/* Modal for Viewing Details - បានកែប្រែបន្ថែមតារាង និង Layout ថ្មី */}
      <Modal
        title={
          <b className='text-lg text-indigo-600'>Purchase Order Details</b>
        }
        open={state.openDetail}
        onCancel={() => setState(pre => ({ ...pre, openDetail: false }))}
        footer={[
          <Button
            key='close'
            onClick={() => setState(pre => ({ ...pre, openDetail: false }))}
          >
            Close
          </Button>
        ]}
        width={750}
        centered
      >
        {state.viewDetail && (
          <div className='py-2'>
            <Row gutter={[16, 12]}>
              <Col span={12}>
                <Text type='secondary'>Invoice No:</Text> <br />
                <b className='text-base text-gray-800'>
                  {state.viewDetail.purchase_no}
                </b>
              </Col>
              <Col span={12}>
                <Text type='secondary'>Purchase Date:</Text> <br />
                <b>
                  {dayjs(state.viewDetail.purchase_date).format('DD-MM-YYYY')}
                </b>
              </Col>
              <Col span={12}>
                <Text type='secondary'>Supplier:</Text> <br />
                <b>{state.viewDetail.supplier?.name || 'N/A'}</b>
              </Col>
              <Col span={12}>
                <Text type='secondary'>Payment Method:</Text> <br />
                <b>{state.viewDetail.payment_method?.name || 'N/A'}</b>
              </Col>
            </Row>

            <Divider orientation='left' style={{ fontSize: 13, marginTop: 20 }}>
              Item List
            </Divider>

            {/* តារាងបង្ហាញបញ្ជីមុខទំនិញដែលបានទិញចូលជាក់ស្តែង */}
            <Table
              dataSource={state.viewDetail.purchase_items || []}
              rowKey='id'
              pagination={false}
              size='small'
              columns={[
                {
                  title: 'Product Name',
                  render: (_, row) => row.product?.product_name || 'N/A'
                },
                { title: 'Qty', dataIndex: 'quantity', align: 'center' },
                {
                  title: 'Cost',
                  dataIndex: 'purchase_unit_cost',
                  render: v => `$${Number(v).toFixed(2)}`
                },
                {
                  title: 'Subtotal',
                  dataIndex: 'sub_total',
                  render: v => <Text strong>${Number(v).toFixed(2)}</Text>
                },
                {
                  title: 'Expiry Date',
                  dataIndex: 'expiry_date',
                  render: d => (d ? dayjs(d).format('DD-MM-YYYY') : '-')
                }
              ]}
            />

            <Divider />

            {/* បង្ហាញព័ត៌មានតម្លៃសរុប និងប្រាក់ជំពាក់ */}
            <Row justify='end'>
              <Col span={12}>
                <div className='flex justify-between mb-1'>
                  <Text type='secondary'>Discount:</Text>
                  <Text strong>
                    ${Number(state.viewDetail.discount || 0).toFixed(2)}
                  </Text>
                </div>
                <div className='flex justify-between mb-1'>
                  <Text type='secondary'>Tax:</Text>
                  <Text strong>
                    ${Number(state.viewDetail.tax || 0).toFixed(2)}
                  </Text>
                </div>
                <div className='flex justify-between border-t pt-2 mt-1 mb-1'>
                  <Text strong>Grand Total:</Text>
                  <Text strong className='text-indigo-600'>
                    ${Number(state.viewDetail.grand_total).toFixed(2)}
                  </Text>
                </div>
                <div className='flex justify-between mb-1'>
                  <Text type='success'>Paid Amount:</Text>
                  <Text type='success' strong>
                    ${Number(state.viewDetail.paid_amount).toFixed(2)}
                  </Text>
                </div>
                <div className='flex justify-between'>
                  <Text type='danger'>Due Amount:</Text>
                  <Text type='danger' strong>
                    ${Number(state.viewDetail.due_amount).toFixed(2)}
                  </Text>
                </div>
              </Col>
            </Row>
          </div>
        )}
      </Modal>
    </div>
  )
}

export default PurchasePage
