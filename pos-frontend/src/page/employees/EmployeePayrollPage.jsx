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
  DatePicker
} from 'antd'
import {
  FilterOutlined,
  ReloadOutlined,
  SearchOutlined,
  ExclamationCircleFilled,
  PlusOutlined,
  EyeOutlined
} from '@ant-design/icons'
import { CiEdit } from 'react-icons/ci'
import { RiSave3Fill } from 'react-icons/ri'
import { MdDelete } from 'react-icons/md'
import { BiSolidEditAlt } from 'react-icons/bi'
import { FiDownload } from 'react-icons/fi'

import { isPermissionAction } from '../../utils/helper'
import { PurchaseService } from '../../services/purchaseService'

import PageLoader from '../../component/common/PageLoader'
import ServerErrorPage from '../error-page/500'
import { exportFile } from '../../utils/exportFile'

const { Title, Text } = Typography

function PurchasePage () {
  const [formRef] = Form.useForm()
  const [state, setState] = useState({
    list: [],
    total: 0,
    loading: false,
    open: false,
    openDetail: false,
    viewDetail: null,
    suppliers: [],
    payment_methods: [],
    products: []
  })
  const [isServerError, setIsServerError] = useState(false)
  const [filter, setFilter] = useState({
    txt_search: '',
    supplier_id: null,
    payment_status: null,
    page: 1,
    limit: 10
  })

  useEffect(() => {
    getList()
  }, [])

  const getList = async (param_filter = {}) => {
    const currentFilter = {
      ...filter,
      ...param_filter
    }
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
        const status = res?.errors?.status
        if (status === 500) {
          setIsServerError(true)
        } else if (status === 403) {
          setIsServerError(false)
          message.error("You don't have permission to view this list.")
        } else {
          message.error(res?.errors?.message || 'Something went wrong!')
        }
      }
    } catch (error) {
      setIsServerError(true)
    } finally {
      setState(pre => ({ ...pre, loading: false }))
    }
  }

  const onFinish = async values => {
    try {
      let res = values.id
        ? await PurchaseService.update(values.id, values)
        : await PurchaseService.create(values)

      if (res && !res.errors) {
        message.success(res.message || 'Success!')
        handleCloseModal()
        getList()
      } else {
        message.error(res?.errors?.message || 'Operation failed!')
      }
    } catch (error) {
      message.error('Server error occurred.')
    }
  }

  const handleEdit = data => {
    formRef.setFieldsValue({ ...data })
    setState(pre => ({ ...pre, open: true }))
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

  const handleDelete = async data => {
    Modal.confirm({
      title: 'Confirm Deletion',
      icon: <ExclamationCircleFilled style={{ color: '#ff4d4f' }} />,
      content: `Are you sure you want to delete this purchase record?`,
      okText: 'Delete',
      okType: 'danger',
      centered: true,
      onOk: async () => {
        try {
          const res = await PurchaseService.delete(data.id)
          if (res && !res.error) {
            message.success('Deleted successfully!')
            getList()
          }
        } catch (error) {
          message.error('Error deleting.')
        }
      }
    })
  }

  const handleOpenModal = () => {
    setState(pre => ({ ...pre, open: true }))
  }

  const handleCloseModal = () => {
    setState(pre => ({ ...pre, open: false }))
    formRef.resetFields()
  }

  const handleExport = () => {
    exportFile({
      url: 'purchase-export',
      filename: 'Purchase_List'
    })
  }

  const handleReset = () => {
    const data = {
      txt_search: '',
      supplier_id: null,
      payment_status: null,
      page: 1,
      limit: 10
    }
    setFilter(data)
    getList(data)
  }

  const handleFilter = () => {
    getList({ ...filter, page: 1 })
  }

  if (isServerError) return <ServerErrorPage onRetry={() => getList()} />

  return (
    <div className='p-4'>
      {state.loading && <PageLoader />}

      {/* Header */}
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
              Manage product stock purchases, suppliers, and payment states.
            </Text>
          </div>
          <div className='flex flex-wrap items-center gap-3 w-full md:w-auto justify-end'>
            {isPermissionAction('purchase.export') && (
              <Button
                onClick={handleExport}
                className='border-gray-200 hover:text-indigo-600 hover:border-indigo-600 flex items-center'
                icon={<FiDownload />}
              >
                Export Excel
              </Button>
            )}

            {isPermissionAction('purchase.create') && (
              <Button
                type='primary'
                icon={<PlusOutlined />}
                onClick={handleOpenModal}
                className='bg-indigo-600 border-0 hover:bg-indigo-700 flex items-center'
              >
                Add New Purchase
              </Button>
            )}
          </div>
        </div>

        {/* Filters */}
        <div className='border-t border-gray-100 pt-6 flex flex-wrap justify-between items-center gap-4'>
          <Input
            placeholder='Search invoice or info...'
            value={filter.txt_search}
            onChange={e =>
              setFilter(p => ({ ...p, txt_search: e.target.value }))
            }
            onPressEnter={() => getList({ page: 1 })}
            prefix={<SearchOutlined />}
            style={{ width: 250 }}
          />
          <div className='flex flex-wrap items-center gap-3'>
            <Select
              allowClear
              placeholder='Select Supplier'
              style={{ width: 180 }}
              value={filter.supplier_id}
              onChange={value => setFilter(p => ({ ...p, supplier_id: value }))}
              options={state.suppliers?.map(item => ({
                label: item.name,
                value: item.id
              }))}
            />

            <Select
              allowClear
              placeholder='Payment Status'
              style={{ width: 160 }}
              value={filter.payment_status}
              onChange={value => setFilter(p => ({ ...p, payment_status: value }))}
              options={[
                { label: 'Paid', value: 'Paid' },
                { label: 'Pending', value: 'Pending' },
                { label: 'Partial', value: 'Partial' }
              ]}
            />
            <div className='flex gap-2'>
              <Button
                type='default'
                onClick={handleReset}
                icon={<ReloadOutlined />}
                className='px-3 flex items-center'
              >
                Reset
              </Button>
              <Button
                type='primary'
                onClick={handleFilter}
                icon={<FilterOutlined />}
                className='px-3 flex items-center bg-indigo-600 border-0 hover:bg-indigo-700'
              >
                Filter
              </Button>
            </div>
          </div>
        </div>
      </div>

      {/* Modal for Create/Update */}
      <Modal
        title={formRef.getFieldValue('id') ? 'Update Purchase Record' : 'Create New Purchase'}
        open={state.open}
        onCancel={handleCloseModal}
        footer={null}
        centered
        width={750}
      >
        <Form layout='vertical' form={formRef} onFinish={onFinish}>
          <Form.Item name='id' hidden>
            <Input />
          </Form.Item>

          <Row gutter={16}>
            <Col span={12}>
              <Form.Item
                label='Supplier'
                name='supplier_id'
                rules={[{ required: true, message: 'Please select a supplier!' }]}
              >
                <Select
                  placeholder='Select supplier'
                  options={(state.suppliers || []).map(item => ({
                    label: item.name,
                    value: item.id
                  }))}
                />
              </Form.Item>
            </Col>
            <Col span={12}>
              <Form.Item
                label='Payment Method'
                name='payment_method_id'
                rules={[{ required: true, message: 'Please select payment method!' }]}
              >
                <Select
                  placeholder='Select payment method'
                  options={(state.payment_methods || []).map(item => ({
                    label: item.name,
                    value: item.id
                  }))}
                />
              </Form.Item>
            </Col>
          </Row>

          <Row gutter={16}>
            <Col span={12}>
              <Form.Item
                name='total_amount'
                label='Total Amount'
                rules={[{ required: true, message: 'Total amount is required!' }]}
              >
                <InputNumber className='w-full' prefix='$' min={0} />
              </Form.Item>
            </Col>
            <Col span={12}>
              <Form.Item
                name='paid_amount'
                label='Paid Amount'
                rules={[{ required: true, message: 'Paid amount is required!' }]}
              >
                <InputNumber className='w-full' prefix='$' min={0} />
              </Form.Item>
            </Col>
          </Row>

          <Row gutter={16}>
            <Col span={12}>
              <Form.Item
                label='Payment Status'
                name='payment_status'
                rules={[{ required: true, message: 'Select status!' }]}
              >
                <Select
                  placeholder='Select status'
                  options={[
                    { label: 'Paid', value: 'Paid' },
                    { label: 'Pending', value: 'Pending' },
                    { label: 'Partial', value: 'Partial' }
                  ]}
                />
              </Form.Item>
            </Col>
            <Col span={12}>
              <Form.Item name='description' label='Description/Remarks'>
                <Input placeholder='Enter reference or details' />
              </Form.Item>
            </Col>
          </Row>

          <div className='text-right mt-6'>
            <Space>
              <Button onClick={handleCloseModal}>Cancel</Button>
              <Button
                type='primary'
                htmlType='submit'
                icon={formRef.getFieldValue('id') ? <BiSolidEditAlt /> : <RiSave3Fill />}
                className='bg-indigo-600'
              >
                {formRef.getFieldValue('id') ? 'Update' : 'Save'}
              </Button>
            </Space>
          </div>
        </Form>
      </Modal>

      {/* Modal for View Detail */}
      <Modal
        title='Purchase Invoice Detail'
        open={state.openDetail}
        onCancel={() => setState(pre => ({ ...pre, openDetail: false, viewDetail: null }))}
        footer={[
          <Button key='close' onClick={() => setState(pre => ({ ...pre, openDetail: false, viewDetail: null }))}>
            Close
          </Button>
        ]}
        centered
      >
        {state.viewDetail && (
          <div className='space-y-3 pt-3'>
            <p><strong>Invoice ID:</strong> #{state.viewDetail.id}</p>
            <p><strong>Supplier:</strong> {state.viewDetail.supplier?.name || 'N/A'}</p>
            <p><strong>Total Cost:</strong> ${state.viewDetail.total_amount}</p>
            <p><strong>Paid Amount:</strong> ${state.viewDetail.paid_amount}</p>
            <p><strong>Status:</strong> <Tag color={state.viewDetail.payment_status === 'Paid' ? 'green' : 'gold'}>{state.viewDetail.payment_status}</Tag></p>
            <p><strong>Description:</strong> {state.viewDetail.description || 'No description'}</p>
          </div>
        )}
      </Modal>

      {/* Table Section */}
      {isPermissionAction('purchase.view') ? (
        <Table
          dataSource={state.list}
          rowKey='id'
          pagination={{
            current: filter.page,
            pageSize: filter.limit,
            total: state.total,
            showSizeChanger: true,
            pageSizeOptions: ['10', '20', '50'],
            onChange: (page, pageSize) => {
              getList({ page, limit: pageSize })
            }
          }}
          columns={[
            {
              title: 'Invoice ID',
              dataIndex: 'id',
              render: id => <strong>#{id}</strong>
            },
            {
              title: 'Supplier',
              render: (_, row) => row.supplier?.name || 'N/A'
            },
            {
              title: 'Total Amount',
              dataIndex: 'total_amount',
              render: v => `$${v || 0}`
            },
            {
              title: 'Paid Amount',
              dataIndex: 'paid_amount',
              render: v => `$${v || 0}`
            },
            {
              title: 'Status',
              dataIndex: 'payment_status',
              render: status => {
                let color = 'gold'
                if (status === 'Paid') color = 'green'
                if (status === 'Pending') color = 'red'
                return <Tag color={color}>{status?.toUpperCase()}</Tag>
              }
            },
            {
              title: 'Actions',
              align: 'center',
              render: (_, data) => (
                <Space>
                  <Button
                    type='text'
                    icon={<EyeOutlined style={{ fontSize: 16, color: '#4f46e5' }} />}
                    onClick={() => handleShowDetail(data.id)}
                  />
                  {isPermissionAction('purchase.update') && (
                    <Button
                      type='text'
                      onClick={() => handleEdit(data)}
                      icon={<CiEdit style={{ fontSize: 18, color: '#004EBC' }} />}
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
        <div className='text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-300'>
          <Title level={4} type='danger'>
            Access Denied
          </Title>
          <Text type='secondary'>
            You do not have permission to view purchase data!
          </Text>
        </div>
      )}
    </div>
  )
}

export default PurchasePage