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
  InputNumber
} from 'antd'
import {
  FilterOutlined,
  ReloadOutlined,
  SearchOutlined,
  ExclamationCircleFilled,
  PlusOutlined
} from '@ant-design/icons'
import { CiEdit } from 'react-icons/ci'
import { RiSave3Fill } from 'react-icons/ri'
import { MdDelete } from 'react-icons/md'
import { BiSolidEditAlt } from 'react-icons/bi'

import { isPermissionAction } from '../../utils/helper'
import { supplierService } from '../../services/supplierService'
import PageLoader from '../../component/common/PageLoader'
import ServerErrorPage from '../error-page/500'

const { Title, Text } = Typography

function SupplierPage () {
  const [formRef] = Form.useForm()
  const [state, setState] = useState({
    list: [],
    total: 0,
    loading: false,
    open: false
  })
  const [isServerError, setIsServerError] = useState(false)
  const [filter, setFilter] = useState({
    txt_search: '',
    is_active: null
  })

  useEffect(() => {
    getList()
  }, [])

  const getList = async (param_filter = {}) => {
    const currentFilter = { ...filter, ...param_filter }
    setState(pre => ({ ...pre, loading: true }))
    setIsServerError(false)

    try {
      const res = await supplierService.getList(currentFilter)
      if (res && !res.errors) {
        setState(pre => ({
          ...pre,
          total: res.total || 0,
          list: res.list || []
        }))
      } else {
        if (res?.errors?.status === 500) setIsServerError(true)
        else message.error(res?.errors?.message || 'Failed to fetch data')
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
        ? await supplierService.update(values.id, values)
        : await supplierService.create(values)

      if (res && !res.errors) {
        message.success(res.message || 'Success!')
        handleCloseModal()
        getList()
      } else {
        message.error(
          res?.errors?.message || res?.message || 'Operation failed!'
        )
      }
    } catch (error) {
      message.error('Server error occurred.')
    }
  }

  const handleEdit = data => {
    formRef.setFieldsValue({
      ...data
    })
    setState(pre => ({ ...pre, open: true }))
  }

  const handleDelete = async data => {
    Modal.confirm({
      title: 'Confirm Deletion',
      icon: <ExclamationCircleFilled style={{ color: '#ff4d4f' }} />,
      content: `Are you sure you want to delete supplier: ${data.name}?`,
      okText: 'Delete',
      okType: 'danger',
      centered: true,
      onOk: async () => {
        try {
          const res = await supplierService.delete(data.id)
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

  const handleCloseModal = () => {
    setState(pre => ({ ...pre, open: false }))
    formRef.resetFields()
  }

  const handleReset = () => {
    const data = { txt_search: '', is_active: null }
    setFilter(data)
    getList(data)
  }

  if (isServerError) return <ServerErrorPage onRetry={() => getList()} />

  return (
    <div className='p-4'>
      {state.loading && <PageLoader />}

      {/* Header and Filter Block */}
      <div className='bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6'>
        <div className='flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6'>
          <div>
            <h2 className='text-xl font-bold text-gray-900 m-0'>
              Supplier Management
              <span className='ml-2 text-sm font-normal text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full'>
                Total: {state.total}
              </span>
            </h2>
            <Text type='secondary'>
              Manage merchant supply chains, inventory lines, and accounts
              payable.
            </Text>
          </div>
          <div className='flex items-center gap-3'>
            {isPermissionAction('supplier.create') && (
              <Button
                type='primary'
                icon={<PlusOutlined />}
                onClick={() => setState(p => ({ ...p, open: true }))}
                className='bg-indigo-600'
              >
                Add New
              </Button>
            )}
          </div>
        </div>

        <div className='border-t border-gray-100 pt-6 flex flex-wrap justify-between items-center gap-4'>
          <Input
            placeholder='Search name, contact person, or phone...'
            value={filter.txt_search}
            onChange={e =>
              setFilter(p => ({ ...p, txt_search: e.target.value }))
            }
            onPressEnter={() => getList()}
            prefix={<SearchOutlined />}
            style={{ width: 250 }}
          />
          <div className='flex items-center gap-3'>
            <Select
              allowClear
              placeholder='Status Filter'
              style={{ width: 150 }}
              value={filter.is_active}
              onChange={v => setFilter(p => ({ ...p, is_active: v }))}
              options={[
                { label: 'Active', value: 1 },
                { label: 'Inactive', value: 0 }
              ]}
            />
            <Button onClick={handleReset} icon={<ReloadOutlined />}>
              Reset
            </Button>
            <Button
              type='primary'
              onClick={() => getList()}
              icon={<FilterOutlined />}
              className='bg-indigo-600'
            >
              Filter
            </Button>
          </div>
        </div>
      </div>

      {/* Setup Form Modal Window */}
      <Modal
        title={
          formRef.getFieldValue('id')
            ? 'Update Supplier Profile'
            : 'Register New Supplier'
        }
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
                label='Company / Supplier Name'
                name='name'
                rules={[
                  { required: true, message: 'Supplier name is required' }
                ]}
              >
                <Input placeholder='Enter business or vendor name' />
              </Form.Item>
            </Col>
            <Col span={12}>
              <Form.Item label='Contact Person' name='contact_person'>
                <Input placeholder='Enter point of contact name' />
              </Form.Item>
            </Col>
          </Row>

          <Row gutter={16}>
            <Col span={12}>
              <Form.Item
                label='Telephone Contact'
                name='tel'
                rules={[
                  { required: true, message: 'Phone number line is required' }
                ]}
              >
                <Input placeholder='Enter telephone number' />
              </Form.Item>
            </Col>
            <Col span={12}>
              <Form.Item label='Email Address' name='email'>
                <Input placeholder='Enter email address' />
              </Form.Item>
            </Col>
          </Row>

          <Row gutter={16}>
            <Col span={12}>
              <Form.Item label='VAT / TIN Tax Code' name='vat_number'>
                <Input placeholder='Enter corporate tax registration code' />
              </Form.Item>
            </Col>
            <Col span={12}>
              <Form.Item
                label='Status'
                name='is_active'
                initialValue={1}
                rules={[{ required: true }]}
              >
                <Select
                  options={[
                    { label: 'Active / Cooperating', value: 1 },
                    { label: 'Inactive / Suspended', value: 0 }
                  ]}
                />
              </Form.Item>
            </Col>
          </Row>

          {/* Opening Balances are structural fields handled on Creation */}
          {!formRef.getFieldValue('id') && (
            <Row gutter={16}>
              <Col span={24}>
                <Form.Item
                  label='Opening Balance Debt'
                  name='opening_balance'
                  initialValue={0}
                >
                  <InputNumber
                    className='w-full'
                    prefix='$'
                    min={0}
                    placeholder='Current pending debts owed to this supplier'
                  />
                </Form.Item>
              </Col>
            </Row>
          )}

          <Row gutter={16}>
            <Col span={24}>
              <Form.Item label='Operational Office Address' name='address'>
                <Input.TextArea
                  rows={2}
                  placeholder='Enter complete vendor address'
                />
              </Form.Item>
            </Col>
          </Row>

          <div className='text-right mt-6'>
            <Space>
              <Button onClick={handleCloseModal}>Cancel</Button>
              <Button
                type='primary'
                htmlType='submit'
                icon={
                  formRef.getFieldValue('id') ? (
                    <BiSolidEditAlt />
                  ) : (
                    <RiSave3Fill />
                  )
                }
                className='bg-indigo-600'
              >
                {formRef.getFieldValue('id') ? 'Update Vendor' : 'Save Vendor'}
              </Button>
            </Space>
          </div>
        </Form>
      </Modal>

      {/* Responsive Data Table Section */}
      {isPermissionAction('supplier.view') ? (
        <Table
          dataSource={state.list}
          rowKey='id'
          scroll={{ x: 'max-content' }}
          columns={[
            {
              title: 'Supplier Info',
              fixed: 'left',
              render: (_, row) => (
                <div>
                  <div className='font-bold text-gray-900'>{row.name}</div>
                  {row.contact_person && (
                    <Text type='secondary' className='text-xs'>
                      POC: {row.contact_person}
                    </Text>
                  )}
                </div>
              )
            },
            { title: 'Telephone', dataIndex: 'tel' },
            {
              title: 'Email Address',
              dataIndex: 'email',
              render: v => v || '-'
            },
            {
              title: 'Owed Balance',
              dataIndex: 'current_balance',
              render: balance => (
                <span
                  className={
                    balance > 0 ? 'text-red-500 font-semibold' : 'text-gray-600'
                  }
                >
                  ${parseFloat(balance || 0).toFixed(2)}
                </span>
              )
            },
            {
              title: 'VAT Registered',
              dataIndex: 'vat_number',
              render: v => v || '-'
            },
            {
              title: 'Status',
              dataIndex: 'is_active',
              render: is_active => {
                const color = is_active ? 'green' : 'red'
                const label = is_active ? 'ACTIVE' : 'INACTIVE'
                return <Tag color={color}>{label}</Tag>
              }
            },
            { title: 'Address Line', dataIndex: 'address', ellipsis: true },
            {
              title: 'Actions',
              align: 'center',
              width: 110,
              fixed: 'right',
              hidden: !(
                isPermissionAction('supplier.update') ||
                isPermissionAction('supplier.delete')
              ),
              render: (_, data) => (
                <Space>
                  {isPermissionAction('supplier.update') && (
                    <Button
                      type='text'
                      onClick={() => handleEdit(data)}
                      icon={
                        <CiEdit style={{ fontSize: 18, color: '#004EBC' }} />
                      }
                    />
                  )}
                  {isPermissionAction('supplier.delete') && (
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
            You do not have the required structural permissions to access vendor
            ledger profiles!
          </Text>
        </div>
      )}
    </div>
  )
}

export default SupplierPage
