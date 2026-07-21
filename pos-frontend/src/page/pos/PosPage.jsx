import React, { useEffect, useMemo, useState } from 'react'
import {
  Badge,
  Button,
  Col,
  Empty,
  Form,
  Image,
  Input,
  message,
  Row,
  Select,
  Spin,
  Tag,
  Radio,
  Typography,
  Pagination,
  Tooltip
} from 'antd'
import {
  SearchOutlined,
  DeleteOutlined,
  PlusOutlined,
  MinusOutlined,
  ReloadOutlined,
  CheckCircleOutlined,
  ShoppingCartOutlined,
  AppstoreOutlined,
  UserOutlined,
  CreditCardOutlined,
  TagsOutlined
} from '@ant-design/icons'
import config from '../../utils/config'
import { OrderService } from '../../services/orderService'

const { Text, Title } = Typography

const PosPage = () => {
  const [form] = Form.useForm()
  const [state, setState] = useState({
    loading: false,
    submitting: false,
    products: [],
    customers: [],
    paymentMethods: [],
    category: [],
    cartItems: []
  })
  
  const [filter, setFilter] = useState({
    txt_search: '',
    category_id: null
  })

  // Pagination state
  const [currentPage, setCurrentPage] = useState(1)
  const [pageSize, setPageSize] = useState(8)

  // Helper for product image URL
  const getProductImage = (image) => {
    if (!image) return 'https://placehold.co/300x300?text=No+Image';
    if (image.startsWith('http')) return image;
    const cleanPath = image.replace(/^storage\//, '').replace(/^public\//, '');
    return `${config.image_path}${cleanPath}`;
  };

  // GET DATA
  const getList = async (param = {}) => {
    const currentFilter = { ...filter, ...param }
    setFilter(currentFilter)
    setState(prev => ({ ...prev, loading: true }))

    try {
      const res = await OrderService.getList(currentFilter)

      if (res && !res.errors) {
        setState(prev => ({
          ...prev,
          products: res.products || [],
          customers: res.customers || [],
          paymentMethods: res.payment_methods || [],
          category: res.category || []
        }))
      } else {
        message.error(res?.errors?.message || 'Failed loading POS data')
      }
    } catch (error) {
      message.error('Server Error loading POS data')
    } finally {
      setState(prev => ({ ...prev, loading: false }))
    }
  }

  useEffect(() => {
    getList()
  }, [])

  // Reset to page 1 whenever filter or search changes
  useEffect(() => {
    setCurrentPage(1)
  }, [filter.txt_search, filter.category_id])

  const filteredProducts = useMemo(() => {
    const keyword = filter.txt_search?.toLowerCase() || ''

    return state.products.filter(item => {
      const matchSearch =
        item.product_name?.toLowerCase().includes(keyword) ||
        item.sku?.toLowerCase().includes(keyword) ||
        item.barcode?.toLowerCase().includes(keyword)

      const matchCategory = !filter.category_id || item.category_id === filter.category_id

      return matchSearch && matchCategory
    })
  }, [state.products, filter.txt_search, filter.category_id])

  // Paginated Products Slice
  const paginatedProducts = useMemo(() => {
    const startIndex = (currentPage - 1) * pageSize
    return filteredProducts.slice(startIndex, startIndex + pageSize)
  }, [filteredProducts, currentPage, pageSize])

  // ADD TO CART
  const addToCart = product => {
    const stockQty = product.quantity ?? product.stock_quantity ?? 0;
    if (stockQty <= 0) {
      return message.warning('Product is out of stock')
    }

    const found = state.cartItems.find(i => i.id === product.id)

    if (found) {
      if (found.quantity >= stockQty) {
        return message.warning('Stock limit reached for this item')
      }

      setState(prev => ({
        ...prev,
        cartItems: prev.cartItems.map(item =>
          item.id === product.id
            ? {
                ...item,
                quantity: item.quantity + 1,
                sub_total: (item.quantity + 1) * item.price
              }
            : item
        )
      }))
    } else {
      const itemPrice = Number(product.final_price || product.price || 0);
      setState(prev => ({
        ...prev,
        cartItems: [
          ...prev.cartItems,
          {
            id: product.id,
            product_id: product.id,
            product_name: product.product_name,
            image: product.image,
            price: itemPrice,
            quantity: 1,
            sub_total: itemPrice,
            stock_quantity: stockQty
          }
        ]
      }))
    }
  }

  // UPDATE QTY
  const updateQty = (id, type) => {
    const updated = state.cartItems.map(item => {
      if (item.id === id) {
        let qty = type === 'plus' ? item.quantity + 1 : item.quantity - 1

        if (qty <= 0) qty = 1
        if (qty > item.stock_quantity) {
          qty = item.stock_quantity
          message.warning('Stock limit reached')
        }

        return {
          ...item,
          quantity: qty,
          sub_total: qty * item.price
        }
      }
      return item
    })

    setState(prev => ({ ...prev, cartItems: updated }))
  }

  // REMOVE ITEM
  const removeItem = id => {
    setState(prev => ({
      ...prev,
      cartItems: prev.cartItems.filter(i => i.id !== id)
    }))
  }

  // TOTALS
  const totalQty = useMemo(
    () => state.cartItems.reduce((a, b) => a + b.quantity, 0),
    [state.cartItems]
  )

  const subTotal = useMemo(
    () => state.cartItems.reduce((a, b) => a + b.sub_total, 0),
    [state.cartItems]
  )

  const rawDiscount = Form.useWatch('discount', form) || 0
  const discountType = Form.useWatch('discount_type', form) || 'fixed'

  const discountAmount = useMemo(() => {
    const val = Number(rawDiscount) || 0
    if (discountType === 'percentage') {
      return (subTotal * val) / 100
    }
    return val
  }, [subTotal, rawDiscount, discountType])

  const grandTotal = useMemo(() => {
    const total = subTotal - discountAmount
    return total > 0 ? total : 0
  }, [subTotal, discountAmount])

  // HANDLE CHECKOUT
  const handleCheckout = async () => {
    try {
      const values = await form.validateFields()

      if (!state.cartItems.length) {
        return message.warning("Cart is empty. Please add items to checkout.")
      }

      setState(prev => ({ ...prev, submitting: true }))

      const payload = {
        customer_id: values.customer_id,
        payment_method_id: values.payment_method_id,
        currency_id: 1,
        total_amount: Number(subTotal),
        discount: Number(discountAmount),
        grand_total: Number(grandTotal),
        order_status: "completed",
        items: state.cartItems.map(i => ({
          product_id: i.product_id,
          quantity: i.quantity,
          unit_price: i.price,
          sub_total: i.sub_total
        }))
      }

      const res = await OrderService.create(payload)

      if (res && (res.data || res.status === 'success' || !res.error)) {
        message.success("Order completed successfully!")

        setState(prev => ({
          ...prev,
          cartItems: []
        }))
        form.resetFields()
        // Refresh product stock list from backend
        getList()
      } else {
        message.error(res?.errors?.message || "Checkout failed")
      }

    } catch (err) {
      console.log("CHECKOUT ERROR:", err)
      message.error(
        err.response?.data?.message || err.message || "Please complete required customer and payment details"
      )
    } finally {
      setState(prev => ({ ...prev, submitting: false }))
    }
  }

  return (
    <div className='bg-[#f8fafc] w-full max-w-full overflow-hidden p-3 md:p-5 font-sans min-h-[calc(100vh-64px)] flex flex-col justify-between'>
      <Spin spinning={state.loading}>
        <div className='flex flex-col lg:flex-row gap-5 items-start w-full'>
          
          {/* LEFT SECTION - PRODUCT CATALOG */}
          <div className='flex-1 w-full bg-white rounded-3xl p-4 md:p-5 border border-slate-100 shadow-sm flex flex-col justify-between min-h-[740px] overflow-hidden'>
            <div>
              {/* Category Pills Header */}
              <div className='mb-4 flex items-center gap-2 overflow-x-auto pb-2 scrollbar-thin scrollbar-thumb-slate-200'>
                <Button
                  size="medium"
                  icon={<AppstoreOutlined />}
                  className={`rounded-xl font-bold border-none transition-all px-4 ${
                    !filter.category_id
                      ? 'bg-indigo-600 text-white shadow-md'
                      : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                  }`}
                  onClick={() => setFilter({ ...filter, category_id: null })}
                >
                  All Products ({state.products.length})
                </Button>
                {state.category.map(cat => (
                  <Button
                    key={cat.id}
                    size="medium"
                    className={`rounded-xl font-semibold border-none transition-all px-4 ${
                      filter.category_id === cat.id
                        ? 'bg-indigo-600 text-white shadow-md'
                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                    }`}
                    onClick={() => setFilter({ ...filter, category_id: cat.id })}
                  >
                    {cat.name}
                  </Button>
                ))}
              </div>

              {/* Search Bar */}
              <Input
                size='large'
                placeholder='Scan barcode or search product by code, name, or SKU...'
                prefix={<SearchOutlined className="text-indigo-500 text-lg mr-1" />}
                className='rounded-2xl mb-4 h-12 shadow-2xs border-slate-200 bg-slate-50/70 focus:bg-white text-sm'
                value={filter.txt_search}
                onChange={e => setFilter({ ...filter, txt_search: e.target.value })}
                allowClear
              />

              {/* Product Cards Grid */}
              {paginatedProducts.length > 0 ? (
                <Row gutter={[14, 14]}>
                  {paginatedProducts.map(product => {
                    const stockQty = product.quantity ?? product.stock_quantity ?? 0;
                    const isOutOfStock = stockQty <= 0;

                    return (
                      <Col key={product.id} xs={12} sm={8} md={8} xl={6}>
                        <div
                          onClick={() => addToCart(product)}
                          className={`group bg-white p-3 rounded-2xl border transition-all duration-300 cursor-pointer ${
                            isOutOfStock
                              ? 'border-slate-100 opacity-50'
                              : 'border-slate-100 hover:border-indigo-500 hover:shadow-xl hover:-translate-y-1'
                          } relative overflow-hidden flex flex-col justify-between h-[275px]`}
                        >
                          {/* Price & Discount Badges */}
                          <div className='absolute top-2.5 left-2.5 z-10 flex flex-col gap-1'>
                            <Tag color='indigo' className='rounded-full font-extrabold shadow-sm border-none px-2.5 py-0.5 text-xs bg-indigo-600 text-white m-0'>
                              ${product.final_price || product.price}
                            </Tag>
                            {product.discount_percent > 0 && (
                              <Tag color='rose' className='rounded-full font-bold shadow-sm border-none text-[10px] bg-rose-500 text-white m-0'>
                                -{product.discount_percent}%
                              </Tag>
                            )}
                          </div>

                          {/* Stock Status Badge */}
                          <div className='absolute top-2.5 right-2.5 z-10'>
                            <span
                              className={`px-2 py-0.5 rounded-full text-[10px] font-extrabold shadow-2xs ${
                                !isOutOfStock
                                  ? 'bg-emerald-50 text-emerald-600 border border-emerald-200'
                                  : 'bg-rose-50 text-rose-600 border border-rose-200'
                              }`}
                            >
                              {!isOutOfStock ? `${stockQty} In Stock` : 'Out of Stock'}
                            </span>
                          </div>

                          {/* Product Image */}
                          <div className='h-[135px] w-full my-1 overflow-hidden rounded-xl bg-slate-50 flex items-center justify-center p-2'>
                            <Image
                              src={getProductImage(product.image)}
                              className='w-full h-full object-contain transition-transform group-hover:scale-105 duration-300'
                              style={{
                                width: '100%',
                                height: '100%',
                                objectFit: 'contain',
                              }}
                              preview={false}
                              fallback="https://placehold.co/300x300?text=No+Image"
                            />
                          </div>

                          {/* Product Details */}
                          <div>
                            <h4 className='font-bold text-slate-800 text-xs truncate mb-0.5' title={product.product_name}>
                              {product.product_name}
                            </h4>
                            <p className='text-[10px] text-slate-400 mb-1.5 font-mono truncate'>
                              {product.sku || 'SKU N/A'}
                            </p>

                            <div className='flex items-center justify-between pt-1 border-t border-slate-100'>
                              <span className='text-xs font-bold text-slate-700'>
                                ${product.price}
                              </span>
                              <button
                                disabled={isOutOfStock}
                                className={`w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold transition-all ${
                                  !isOutOfStock
                                    ? 'bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white shadow-2xs'
                                    : 'bg-slate-100 text-slate-400 cursor-not-allowed'
                                }`}
                              >
                                <PlusOutlined />
                              </button>
                            </div>
                          </div>
                        </div>
                      </Col>
                    );
                  })}
                </Row>
              ) : (
                <Empty description="No matching products found" className="my-20" />
              )}
            </div>

            {/* Pagination Controls Footer */}
            {filteredProducts.length > 0 && (
              <div className="pt-3 mt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <Text type="secondary" className="text-xs font-medium">
                  Showing <b className="text-slate-800">{(currentPage - 1) * pageSize + 1}</b> to{" "}
                  <b className="text-slate-800">{Math.min(currentPage * pageSize, filteredProducts.length)}</b> of{" "}
                  <b className="text-slate-800">{filteredProducts.length}</b> products
                </Text>

                <Pagination
                  current={currentPage}
                  pageSize={pageSize}
                  total={filteredProducts.length}
                  onChange={(page, pSize) => {
                    setCurrentPage(page);
                    setPageSize(pSize);
                  }}
                  showSizeChanger
                  pageSizeOptions={['8', '12', '16', '24']}
                  className="font-semibold"
                  size="small"
                />
              </div>
            )}
          </div>

          {/* RIGHT SECTION - CHECKOUT CART */}
          <div className='w-full lg:w-[360px] xl:w-[390px] flex-shrink-0 bg-white rounded-3xl shadow-sm border border-slate-100 flex flex-col justify-between min-h-[740px]'>
            
            {/* Cart Header */}
            <div>
              <div className='p-4 border-b flex justify-between items-center bg-slate-50/60 rounded-t-3xl'>
                <div className='flex items-center gap-2'>
                  <ShoppingCartOutlined className="text-indigo-600 text-xl" />
                  <Title level={5} className='!m-0 font-extrabold text-slate-800'>
                    Current Order
                  </Title>
                </div>
                <Badge
                  count={totalQty}
                  showZero
                  className='bg-indigo-600 text-white rounded-lg px-2.5 py-0.5 font-extrabold text-xs'
                />
              </div>

              {/* Table Column Labels */}
              <div className='grid grid-cols-12 px-4 py-2 bg-slate-100/60 text-slate-500 text-[10px] font-extrabold uppercase tracking-wider border-b'>
                <div className='col-span-5'>Product</div>
                <div className='col-span-3 text-center'>Qty</div>
                <div className='col-span-4 text-right'>Sub Total</div>
              </div>

              {/* Cart Items Scrollable List */}
              <div className='max-h-[260px] overflow-y-auto p-3 space-y-2.5'>
                {state.cartItems.length === 0 && (
                  <Empty
                    description={<span className="text-slate-400 text-xs">Cart is empty.<br />Click products to add them to order.</span>}
                    className="my-8"
                  />
                )}
                {state.cartItems.map(item => (
                  <div
                    key={item.id}
                    className='grid grid-cols-12 items-center gap-2 bg-slate-50 p-2 rounded-xl border border-slate-100 hover:border-slate-200 transition-all'
                  >
                    {/* Thumbnail & Title */}
                    <div className='col-span-5 flex items-center gap-2 min-w-0'>
                      <Image
                        src={getProductImage(item.image)}
                        width={34}
                        height={34}
                        className='rounded-lg object-contain bg-white border p-0.5 flex-shrink-0'
                        preview={false}
                        fallback="https://placehold.co/60x60"
                      />
                      <div className="truncate min-w-0">
                        <div className='font-bold text-xs text-slate-800 truncate' title={item.product_name}>
                          {item.product_name}
                        </div>
                        <div className="text-[10px] text-slate-400">${item.price}</div>
                      </div>
                    </div>

                    {/* Qty Controls */}
                    <div className='col-span-3 flex justify-center'>
                      <div className='flex items-center border rounded-lg bg-white shadow-2xs'>
                        <Button
                          type='text'
                          size='small'
                          icon={<MinusOutlined className="text-[9px]" />}
                          onClick={() => updateQty(item.id, 'minus')}
                        />
                        <span className='px-1 font-extrabold text-xs text-slate-800'>
                          {item.quantity}
                        </span>
                        <Button
                          type='text'
                          size='small'
                          icon={<PlusOutlined className="text-[9px]" />}
                          onClick={() => updateQty(item.id, 'plus')}
                        />
                      </div>
                    </div>

                    {/* Subtotal & Delete */}
                    <div className='col-span-4 flex items-center justify-end gap-1'>
                      <span className='font-extrabold text-indigo-600 text-xs'>
                        ${item.sub_total.toFixed(2)}
                      </span>
                      <Tooltip title="Remove item">
                        <Button
                          danger
                          type='text'
                          size='small'
                          icon={<DeleteOutlined className="text-xs" />}
                          onClick={() => removeItem(item.id)}
                        />
                      </Tooltip>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Form Controls & Summary */}
            <div className='p-4 bg-slate-50/70 border-t rounded-b-3xl'>
              <Form form={form} layout='vertical' size="large">
                <Form.Item
                  name="customer_id"
                  className="mb-2.5"
                  rules={[{ required: true, message: "Please select customer" }]}
                >
                  <Select
                    placeholder="Select Customer"
                    suffixIcon={<UserOutlined className="text-slate-400" />}
                    showSearch
                    optionFilterProp="label"
                    className="rounded-xl text-xs"
                    options={state.customers.map(c => ({
                      label: `${c.first_name} ${c.last_name} (${c.tel})`,
                      value: c.id
                    }))}
                  />
                </Form.Item>

                <Form.Item
                  name="payment_method_id"
                  className="mb-2.5"
                  rules={[{ required: true, message: "Please select payment method" }]}
                >
                  <Select
                    placeholder="Select Payment Method"
                    suffixIcon={<CreditCardOutlined className="text-slate-400" />}
                    className="rounded-xl text-xs"
                    options={state.paymentMethods.map(p => ({
                      label: p.name,
                      value: p.id
                    }))}
                  />
                </Form.Item>

                {/* Discount Header */}
                <div className='flex items-center justify-between gap-2 mb-1.5'>
                  <span className='font-semibold text-xs text-slate-600 flex items-center gap-1'>
                    <TagsOutlined /> Discount:
                  </span>
                  <Form.Item name='discount_type' initialValue='fixed' className='mb-0'>
                    <Radio.Group size="small">
                      <Radio.Button value='fixed'>Fixed ($)</Radio.Button>
                      <Radio.Button value='percentage'>Percent (%)</Radio.Button>
                    </Radio.Group>
                  </Form.Item>
                </div>

                <Form.Item name='discount' className="mb-3">
                  <Input
                    placeholder='0.00'
                    suffix={discountType === 'percentage' ? '%' : '$'}
                    className='rounded-xl h-9 text-xs'
                  />
                </Form.Item>
              </Form>

              {/* Summary Card */}
              <div className='bg-white p-3 rounded-2xl border border-slate-200/90 space-y-1.5 mb-3 shadow-2xs'>
                <div className='flex justify-between text-xs text-slate-500'>
                  <span>Total Quantity:</span> <b className="text-slate-800">{totalQty} units</b>
                </div>
                <div className='flex justify-between text-xs text-slate-500'>
                  <span>Sub Total:</span> <b className="text-slate-800">${subTotal.toFixed(2)}</b>
                </div>
                {discountAmount > 0 && (
                  <div className='flex justify-between text-xs text-rose-500'>
                    <span>Discount Amount:</span> <b>-${discountAmount.toFixed(2)}</b>
                  </div>
                )}
                <div className='flex justify-between text-base font-black text-slate-900 pt-1.5 border-t border-slate-100'>
                  <span>Grand Total:</span> <span className="text-indigo-600">${grandTotal.toFixed(2)}</span>
                </div>
              </div>

              {/* Action Buttons */}
              <div className='flex gap-2'>
                <Button
                  size='large'
                  className='rounded-xl bg-slate-200 text-slate-700 hover:bg-slate-300 border-none font-bold'
                  icon={<ReloadOutlined />}
                  onClick={() => {
                    setState(prev => ({ ...prev, cartItems: [] }))
                    form.resetFields()
                  }}
                >
                  Reset
                </Button>
                <Button
                  size='large'
                  type='primary'
                  block
                  loading={state.submitting}
                  className='rounded-xl bg-emerald-500 hover:bg-emerald-600 border-none font-bold h-11 text-base shadow-md'
                  icon={<CheckCircleOutlined />}
                  onClick={handleCheckout}
                >
                  Pay Now
                </Button>
              </div>
            </div>
          </div>

        </div>
      </Spin>
    </div>
  )
}

export default PosPage
