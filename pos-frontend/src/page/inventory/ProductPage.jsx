import React, { useEffect, useState } from 'react'
import {
  Button,
  Col,
  Form,
  Image,
  Input,
  message,
  Modal,
  Select,
  Space,
  Table,
  Tag,
  Upload,
  Row,
  Typography
} from 'antd'
import {
  FilterOutlined,
  PlusOutlined,
  ReloadOutlined,
  ExclamationCircleFilled,
  UploadOutlined,
  FileSearchOutlined,
  SearchOutlined,
  ImportOutlined,
  ExportOutlined,
  FileExcelOutlined
} from '@ant-design/icons'
import { CiEdit } from 'react-icons/ci'
import { RiSave3Fill } from 'react-icons/ri'
import { MdDelete } from 'react-icons/md'
import { BiSolidEditAlt } from 'react-icons/bi'
import { FiDownload } from 'react-icons/fi'
// Utils
import { request } from '../../utils/request'
import config from '../../utils/config'
import { usePreviewStore } from '../../store/previewStore'
import { isPermissionAction } from '../../utils/helper'
import { exportFile } from '../../utils/exportFile'

// Components
import MainPage from '../../component/common/PageLoader'
import UploadButton from '../../component/ui/UploadButton'
import PageLoader from '../../component/common/PageLoader'

const { Title, Text } = Typography

function ProductPage () {
  const [form] = Form.useForm()
  const [state, setState] = useState({
    list: [],
    category: [],
    brand: [],
    total: [],
    loading: false,
    open: false
  })

  const [filter, setFilter] = useState({
    txt_search: null,
    status: null,
    category_id: null,
    brand_id: null
  })
  const [galleryList, setGalleryList] = useState([])
  const [validate, setValidate] = useState({})
  const [fileList, setFileList] = useState([])

  // Zustand Store
  const { open, imgUrl, handleOpenPreview, handleClosePreview } =
    usePreviewStore()

  const handlePreview = async file => {
    let src = file.url || file.thumbUrl

    if (!src && file.originFileObj) {
      src = await getBase64(file.originFileObj)
    }
    handleOpenPreview(src)
  }

  const getBase64 = file =>
    new Promise((resolve, reject) => {
      const reader = new FileReader()
      reader.readAsDataURL(file)
      reader.onload = () => resolve(reader.result)
      reader.onerror = error => reject(error)
    })

  const getList = async param_filter => {
    param_filter = {
      ...filter,
      ...param_filter
    }
    setState(pre => ({ ...pre, loading: true }))
    let query_param = '?page=1'
    if (param_filter.txt_search !== null && param_filter.txt_search !== '') {
      query_param += '&txt_search=' + param_filter.txt_search
    }
    if (param_filter.status !== null && param_filter.status !== '') {
      query_param += '&status=' + param_filter.status
    }
    if (param_filter.category_id) {
      query_param += '&category_id=' + param_filter.category_id
    }
    if (param_filter.brand_id) {
      query_param += '&brand_id=' + param_filter.brand_id
    }

    const res = await request('products' + query_param, 'get', {})
    if (res && !res.errors) {
      setState(pre => ({
        ...pre,
        total: res.total,
        list: res.list || [],
        category: res.category || [],
        brand: res.brand || [],
        loading: false
      }))
    } else {
      setState(pre => ({ ...pre, loading: false }))
      if (res.errors?.message) {
        message.error(res.errors?.message)
      }
    }
  }

  useEffect(() => {
    getList()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  const handleOpenModal = () => {
    setState(pre => ({ ...pre, open: true }))
  }

  const handleCloseModal = () => {
    setState(pre => ({ ...pre, open: false }))
    form.resetFields()
    setFileList([])
    setGalleryList([])
    setValidate({})
  }

  const onFinish = async item => {
    const formData = new FormData()

    formData.append('category_id', item.category_id || '')
    formData.append('brand_id', item.brand_id || '')
    formData.append('product_name', item.product_name || '')
    formData.append('slug', item.slug || '')

    formData.append('sku', item.sku || 'N/A')
    formData.append('barcode', item.barcode || '')
    formData.append('description', item.description || '')

    formData.append('cost_price', item.cost_price ?? 0)
    formData.append('price', item.price ?? 0)
    formData.append('discount_percent', item.discount_percent ?? 0)

    formData.append('stock_quantity', item.stock_quantity ?? 0)
    formData.append('min_stock_alert', item.min_stock_alert ?? 0)

    formData.append('status', item.status ? 1 : 0)
    formData.append('is_featured', item.is_featured ? 1 : 0)

    if (item.weight !== undefined && item.weight !== null) {
      formData.append('weight', item.weight)
    }

    if (fileList.length > 0 && fileList[0].originFileObj) {
      formData.append('image', fileList[0].originFileObj)
    } else if (fileList.length === 0 && form.getFieldValue('id')) {
      formData.append('image_remove', 1)
    }

    galleryList.forEach(file => {
      if (file.originFileObj) {
        formData.append('gallery[]', file.originFileObj)
      } else {
        formData.append('old_gallery[]', file.name)
      }
    })
    let url = 'products'
    let method = 'post'

    if (form.getFieldValue('id')) {
      url += '/' + form.getFieldValue('id')
      formData.append('_method', 'PUT')
    }

    setState(p => ({
      ...p,
      loading: true
    }))

    try {
      const res = await request(url, method, formData)

      if (res && !res.error) {
        message.success(res.message || 'Successfully saved!')
        handleCloseModal()
        getList()
      } else {
        if (res?.errors) {
          setValidate(res.errors)
          const errObj = Object.values(res.errors)[0]
          const errMsg = res.errors.message || (typeof errObj === 'object' ? errObj?.help || errObj : errObj) || 'Validation failed'
          message.error(typeof errMsg === 'string' ? errMsg : 'Validation failed')
        } else {
          message.error(res?.message || 'Failed to perform action!')
        }
      }
    } catch (error) {
      message.error('An unexpected error occurred. Please check your network.')
    } finally {
      setState(p => ({
        ...p,
        loading: false
      }))
    }
  }

  const handleDelete = async data => {
    Modal.confirm({
      title: 'Confirm Deletion',
      icon: <ExclamationCircleFilled style={{ color: '#ff4d4f' }} />,
      content: (
        <div>
          Are you sure you want to delete the product{' '}
          <b>"{data.product_name || data.title}"</b>?
          <p style={{ color: '#8c8c8c', fontSize: '12px', marginTop: '8px' }}>
            * This action cannot be undone.
          </p>
        </div>
      ),
      okText: 'Delete',
      okType: 'danger',
      cancelText: 'Cancel',
      centered: true,
      onOk: async () => {
        const res = await request(`products/${data.id}`, 'delete', {})
        if (res && !res.errors) {
          message.success(res.message || 'Delete Success!')
          getList()
        } else {
          message.error(res?.message || 'Failed to delete!')
        }
      }
    })
  }

  const handleEdit = data => {
    form.setFieldsValue({
      ...data
    })

    form.setFieldsValue({
      status: Number(data.status),
      is_featured: Number(data.is_featured)
    })

    // main image
    if (data.image) {
      setFileList([
        {
          uid: '-1',
          name: data.image,
          status: 'done',
          url: config.image_path + data.image,
          thumbUrl: config.image_path + data.image
        }
      ])
    } else {
      setFileList([])
    }

    // gallery images
    if (Array.isArray(data.gallery) && data.gallery.length > 0) {
      setGalleryList(
        data.gallery.map((img, i) => ({
          uid: i,
          name: img,
          status: 'done',
          url: config.image_path + img,
          thumbUrl: config.image_path + img
        }))
      )
    } else {
      setGalleryList([])
    }

    setState(p => ({
      ...p,
      open: true
    }))
  }

  const handleFilter = () => {
    getList()
  }

  const handleReset = () => {
    const data = {
      txt_search: null,
      status: null,
      category_id: null,
      brand_id: null
    }
    setFilter(data)
    getList(data)
  }
  // function Export
  const handleExport = () => {
    exportFile({
      url: 'product-export',
      filename: 'product_List'
    })
  }
  const slugify = text => {
    return text
      .toString()
      .toLowerCase()
      .trim()
      .replace(/\s+/g, '-')
      .replace(/[^\w-]+/g, '')
      .replace(/--+/g, '-')
  }
  // Helper for product images
  const getProductImage = (image) => {
    if (!image) return null;
    if (image.startsWith('http')) return image;
    const cleanPath = image.replace(/^storage\//, '').replace(/^public\//, '');
    return `${config.image_path}${cleanPath}`;
  };

  return (
    <div>
      {state.loading && <PageLoader />}
      <div>
        <div className='bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6'>
          <div className='flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6'>
            <div>
              <div className='flex flex-wrap items-center gap-3 mb-1'>
                <h1 className='text-2xl font-extrabold text-slate-800 m-0'>
                  Product Management
                </h1>
                <span className='text-xs font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 px-3 py-1 rounded-full'>
                  In Stock: {typeof state.total === 'number' ? state.total : (state.list?.length || 0)}
                </span>
              </div>
              <Text type='secondary' className='text-sm text-slate-500'>
                Manage your products, categories, and inventory stock here.
              </Text>
            </div>

            <div className='flex flex-wrap items-center gap-3 w-full md:w-auto justify-end'>
              {isPermissionAction('product.export') && (
                <Button
                  onClick={handleExport}
                  className='border-gray-200 hover:text-indigo-600 hover:border-indigo-600 flex items-center'
                  icon={<FiDownload />}
                >
                  Export Excel
                </Button>
              )}
              {isPermissionAction('product.create') && (
                <Button
                  type='primary'
                  icon={<PlusOutlined />}
                  onClick={handleOpenModal}
                  className='bg-indigo-600 border-0 hover:bg-indigo-700 flex items-center'
                >
                  Add New
                </Button>
              )}
            </div>
          </div>

          {/* Filter Section */}
          <div className='border-t border-gray-100 pt-6'>
            <div className='flex flex-wrap justify-between items-center gap-4'>
              <Input
                allowClear
                value={filter.txt_search}
                onChange={e =>
                  setFilter(p => ({ ...p, txt_search: e.target.value }))
                }
                placeholder='Search products...'
                onPressEnter={handleFilter}
                prefix={<SearchOutlined className='text-gray-400 mr-2' />}
                style={{ width: 250 }}
              />
              <div className='flex flex-wrap items-center gap-3'>
                <Select
                  allowClear
                  placeholder='Status'
                  style={{ width: 120 }}
                  value={filter.status}
                  onChange={value => setFilter(p => ({ ...p, status: value }))}
                  options={[
                    { label: 'Active', value: 1 },
                    { label: 'Inactive', value: 0 }
                  ]}
                />

                <Select
                  allowClear
                  placeholder='Category'
                  style={{ width: 150 }}
                  value={filter.category_id}
                  onChange={value =>
                    setFilter(p => ({ ...p, category_id: value }))
                  }
                  options={state.category?.map(item => ({
                    label: item.name,
                    value: item.id
                  }))}
                />

                <Select
                  allowClear
                  placeholder='Brand'
                  style={{ width: 150 }}
                  value={filter.brand_id}
                  onChange={value =>
                    setFilter(p => ({ ...p, brand_id: value }))
                  }
                  options={state.brand?.map(item => ({
                    label: item.name,
                    value: item.id
                  }))}
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
        </div>
        <Modal
          title={form.getFieldValue('id') ? 'Update Product' : 'Create Product'}
          open={state.open}
          onCancel={handleCloseModal}
          centered
          width={900}
          footer={null}
          destroyOnClose
        >
          <Form
            layout='vertical'
            form={form}
            onFinish={onFinish}
            // បន្ថែម initialValues នៅត្រង់នេះ
            initialValues={{
              status: 1,
              is_featured: 0
            }}
          >
            <Row gutter={[16, 16]}>
              <Form.Item name='id' hidden>
                <Input />
              </Form.Item>

              <Col span={12}>
                <Form.Item
                  label='Product Name'
                  name='product_name'
                  rules={[{ required: true }]}
                >
                  <Input
                    placeholder='Enter product name'
                    onChange={e => {
                      form.setFieldsValue({ slug: slugify(e.target.value) })
                    }}
                  />
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item label='Slug' name='slug'>
                  <Input placeholder='product-slug-name' />
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item
                  label='Category'
                  name='category_id'
                  rules={[{ required: true }]}
                >
                  <Select
                    placeholder='Select a category'
                    options={state.category?.map(i => ({
                      label: i.name,
                      value: i.id
                    }))}
                  />
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item
                  label='Brand'
                  name='brand_id'
                  rules={[{ required: true }]}
                >
                  <Select
                    placeholder='Select a brand'
                    options={state.brand?.map(i => ({
                      label: i.name,
                      value: i.id
                    }))}
                  />
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item label='SKU' name='sku'>
                  <Input placeholder='Enter SKU code' />
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item label='Barcode' name='barcode'>
                  <Input placeholder='Enter barcode' />
                </Form.Item>
              </Col>

              <Col span={8}>
                <Form.Item label='Cost Price' name='cost_price'>
                  <Input type='number' placeholder='0.00' />
                </Form.Item>
              </Col>

              <Col span={8}>
                <Form.Item
                  label='Price'
                  name='price'
                  rules={[{ required: true }]}
                >
                  <Input type='number' placeholder='0.00' />
                </Form.Item>
              </Col>

              <Col span={8}>
                <Form.Item label='Discount Percent' name='discount_percent'>
                  <Input type='number' placeholder='0' />
                </Form.Item>
              </Col>

              <Col span={8}>
                <Form.Item label='Stock Quantity' name='stock_quantity'>
                  <Input type='number' placeholder='0' />
                </Form.Item>
              </Col>

              <Col span={8}>
                <Form.Item label='Min Stock Alert' name='min_stock_alert'>
                  <Input type='number' placeholder='0' />
                </Form.Item>
              </Col>

              <Col span={8}>
                <Form.Item label='Weight' name='weight'>
                  <Input type='number' placeholder='0' />
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item label='Status' name='status'>
                  <Select placeholder='Select status'>
                    <Select.Option value={1}>Active</Select.Option>
                    <Select.Option value={0}>Inactive</Select.Option>
                  </Select>
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item label='Featured' name='is_featured'>
                  <Select placeholder='Select feature status'>
                    <Select.Option value={0}>No</Select.Option>
                    <Select.Option value={1}>Yes</Select.Option>
                  </Select>
                </Form.Item>
              </Col>

              <Col span={24}>
                <Form.Item label='Description' name='description'>
                  <Input.TextArea
                    rows={3}
                    placeholder='Write product description here...'
                  />
                </Form.Item>
              </Col>

              {/* Upload components remain the same */}
              <Col span={24}>
                <Form.Item label='Main Image'>
                  <Upload
                    listType='picture-card'
                    fileList={fileList}
                    onChange={({ fileList }) => setFileList(fileList)}
                    onPreview={handlePreview}
                    customRequest={({ onSuccess }) => onSuccess('ok')}
                    maxCount={1}
                  >
                    {fileList.length < 1 && <UploadButton />}
                  </Upload>
                </Form.Item>
              </Col>

              <Col span={24}>
                <Form.Item label='Gallery Images'>
                  <Upload
                    listType='picture-card'
                    multiple
                    fileList={galleryList}
                    onChange={({ fileList }) => setGalleryList(fileList)}
                    onPreview={handlePreview}
                    customRequest={({ onSuccess }) => onSuccess('ok')}
                  >
                    + Upload
                  </Upload>
                </Form.Item>
              </Col>
            </Row>

            <div className='flex justify-end gap-2 mt-4'>
              <Button onClick={handleCloseModal}>Cancel</Button>
              <Button type='primary' htmlType='submit'>
                {form.getFieldValue('id') ? 'Update' : 'Save'}
              </Button>
            </div>
          </Form>
        </Modal>

        {isPermissionAction('product.view') ? (
          <Table
            dataSource={state.list}
            rowKey='id'
            scroll={{ x: 1600 }}
            pagination={{
              defaultPageSize: 10,
              showSizeChanger: true,
              pageSizeOptions: ['10', '20', '50']
            }}
            columns={[
              {
                title: 'Product Name',
                dataIndex: 'product_name',
                key: 'product_name',
                fixed: 'left',
                width: 220,
                render: text => <span className="font-bold text-slate-800">{text}</span>
              },
              {
                title: 'Image',
                dataIndex: 'image',
                key: 'image',
                align: 'center',
                width: 90,
                render: image => {
                  const src = getProductImage(image);
                  return src ? (
                    <Image
                      src={src}
                      width={50}
                      height={40}
                      className='rounded-lg border object-contain bg-slate-50 p-0.5'
                      fallback="https://placehold.co/60x60"
                    />
                  ) : (
                    <Text type='secondary' className='text-xs'>
                      No Image
                    </Text>
                  );
                }
              },
              {
                title: 'SKU',
                dataIndex: 'sku',
                key: 'sku',
                width: 120,
                render: v => <span className="font-mono text-xs text-slate-500">{v || '-'}</span>
              },
              {
                title: 'Barcode',
                dataIndex: 'barcode',
                key: 'barcode',
                width: 120,
                render: v => v || '-'
              },
              {
                title: 'Category',
                dataIndex: 'category',
                key: 'category',
                width: 130,
                render: cat => cat?.name ? <Tag color="blue" className="rounded-full">{cat.name}</Tag> : '-'
              },
              {
                title: 'Brand',
                dataIndex: 'brand',
                key: 'brand',
                width: 110,
                render: b => b?.name || '-'
              },
              {
                title: 'Cost',
                dataIndex: 'cost_price',
                key: 'cost_price',
                align: 'right',
                width: 100,
                render: v => (v ? `$${Number(v).toFixed(2)}` : '-')
              },
              {
                title: 'Price',
                dataIndex: 'price',
                key: 'price',
                align: 'right',
                width: 110,
                render: price => (
                  <span className='font-bold text-indigo-600'>
                    ${Number(price || 0).toFixed(2)}
                  </span>
                )
              },
              {
                title: 'Discount',
                dataIndex: 'discount_percent',
                key: 'discount_percent',
                align: 'center',
                width: 90,
                render: v => (v > 0 ? <Tag color="rose" className="bg-rose-50 text-rose-600 border-rose-200">-{v}%</Tag> : '-')
              },
              {
                title: 'Stock Status',
                dataIndex: 'quantity',
                key: 'quantity',
                align: 'center',
                width: 120,
                render: (_, row) => {
                  const qty = row.quantity ?? row.stock_quantity ?? 0;
                  let color = 'green';
                  let label = `${qty} In Stock`;

                  if (qty <= 0) {
                    color = 'red';
                    label = 'Out of Stock';
                  } else if (qty <= 5) {
                    color = 'orange';
                    label = `${qty} Low Stock`;
                  }

                  return <Tag color={color} className="font-bold">{label}</Tag>;
                }
              },
              {
                title: 'Status',
                dataIndex: 'status',
                key: 'status',
                align: 'center',
                width: 100,
                render: value => {
                  const isActive = Number(value) === 1;
                  return (
                    <Tag color={isActive ? 'green' : 'red'} className='rounded-full px-3 font-semibold'>
                      {isActive ? 'Active' : 'Inactive'}
                    </Tag>
                  );
                }
              },
              {
                title: 'Actions',
                align: 'center',
                key: 'actions',
                fixed: 'right',
                width: 110,
                hidden: !(
                  isPermissionAction('product.update') ||
                  isPermissionAction('product.delete')
                ),
                render: (id, data) => (
                  <Space size='small'>
                    {isPermissionAction('product.update') && (
                      <Button
                        type='text'
                        onClick={() => handleEdit(data)}
                        icon={<CiEdit style={{ fontSize: 18, color: '#4f46e5' }} />}
                      />
                    )}
                    {isPermissionAction('product.delete') && (
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
            ].filter(col => !col.hidden)}
          />
        ) : (
          <div className='text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-300'>
            <Title level={4} type='danger'>
              Access Denied
            </Title>
            <Text type='secondary'>
              You do not have permission to view the product data!.
            </Text>
          </div>
        )}
      </div>
    </div>
  )
}
export default ProductPage
