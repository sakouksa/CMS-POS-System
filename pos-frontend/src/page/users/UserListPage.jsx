import React, { useEffect, useState } from 'react';
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
  Card,
  Avatar,
  Row,
  Col,
  Tooltip
} from 'antd';
import {
  UserOutlined,
  SearchOutlined,
  PlusOutlined,
  EditOutlined,
  DeleteOutlined,
  ExclamationCircleFilled,
  LockOutlined,
  MailOutlined,
  PhoneOutlined,
  SafetyCertificateOutlined
} from '@ant-design/icons';
import { request } from '../../utils/request';
import { dateClient } from '../../utils/helper';
import MainPage from '../../component/common/PageLoader';

const RoleColors = {
  'super-admin': 'red',
  admin: 'purple',
  manager: 'blue',
  cashier: 'green',
  accountant: 'orange',
  warehouse: 'cyan',
  hr: 'magenta',
  content: 'geekblue'
};

const UserListPage = () => {
  const [form] = Form.useForm();
  const [state, setState] = useState({
    list: [],
    roles: [],
    total: 0,
    loading: false,
    open: false,
    editingUser: null
  });

  const [filter, setFilter] = useState({
    txt_search: '',
    role_id: null,
    status: null
  });

  useEffect(() => {
    getList();
  }, []);

  const getList = async () => {
    setState(prev => ({ ...prev, loading: true }));
    let query = '?page=1';
    if (filter.txt_search) query += `&txt_search=${encodeURIComponent(filter.txt_search)}`;
    if (filter.role_id) query += `&role_id=${filter.role_id}`;
    if (filter.status !== null && filter.status !== '') query += `&status=${filter.status}`;

    const res = await request(`users${query}`, 'get');
    if (res && !res.errors) {
      setState(prev => ({
        ...prev,
        list: res.list || [],
        total: res.total || 0,
        roles: res.roles || [],
        loading: false
      }));
    } else {
      setState(prev => ({ ...prev, loading: false }));
      message.error(res?.message || 'Failed to fetch users');
    }
  };

  const handleOpenModal = (user = null) => {
    setState(prev => ({ ...prev, open: true, editingUser: user }));
    if (user) {
      const roleId = user.roles && user.roles.length > 0 ? user.roles[0].id : null;
      form.setFieldsValue({
        id: user.id,
        name: user.name,
        email: user.email,
        role_id: roleId,
        status: user.status ?? 1,
        phone: user.profile?.phone || '',
        address: user.profile?.address || ''
      });
    } else {
      form.resetFields();
      form.setFieldsValue({ status: 1 });
    }
  };

  const handleCloseModal = () => {
    setState(prev => ({ ...prev, open: false, editingUser: null }));
    form.resetFields();
  };

  const onFinish = async values => {
    const isEdit = !!state.editingUser;
    const url = isEdit ? `users/${state.editingUser.id}` : 'users';
    const method = isEdit ? 'put' : 'post';

    const res = await request(url, method, values);
    if (res && !res.errors) {
      message.success(res.message || (isEdit ? 'User updated successfully!' : 'User created successfully!'));
      handleCloseModal();
      getList();
    } else {
      message.error(res?.message || 'Operation failed!');
    }
  };

  const handleDelete = user => {
    if (user.id === 1) {
      message.warning('Super Admin account cannot be deleted!');
      return;
    }

    Modal.confirm({
      title: 'Confirm Delete User',
      icon: <ExclamationCircleFilled style={{ color: '#ff4d4f' }} />,
      content: `Are you sure you want to delete user "${user.name}" (${user.email})?`,
      okText: 'Yes, Delete',
      okType: 'danger',
      cancelText: 'Cancel',
      centered: true,
      onOk: async () => {
        const res = await request(`users/${user.id}`, 'delete');
        if (res && !res.errors) {
          message.success(res.message || 'User deleted successfully!');
          getList();
        } else {
          message.error(res?.message || 'Failed to delete user!');
        }
      }
    });
  };

  const columns = [
    {
      title: 'User',
      key: 'user',
      render: record => (
        <Space size='middle'>
          <Avatar
            style={{ backgroundColor: '#1890ff', verticalAlign: 'middle' }}
            icon={<UserOutlined />}
            size='large'
          />
          <div>
            <div className='font-bold text-slate-800 text-sm'>{record.name}</div>
            <div className='text-xs text-slate-400'>{record.email}</div>
          </div>
        </Space>
      )
    },
    {
      title: 'Role',
      key: 'roles',
      render: record => {
        const role = record.roles && record.roles.length > 0 ? record.roles[0] : null;
        if (!role) return <Tag color='default'>No Role</Tag>;
        const color = RoleColors[role.code] || 'blue';
        return <Tag color={color} className='font-semibold uppercase px-3 py-0.5 rounded-full'>{role.name}</Tag>;
      }
    },
    {
      title: 'Phone',
      dataIndex: ['profile', 'phone'],
      key: 'phone',
      render: phone => phone || <span className='text-slate-300'>N/A</span>
    },
    {
      title: 'Status',
      dataIndex: 'status',
      key: 'status',
      align: 'center',
      render: status => (
        <Tag color={status ? 'success' : 'error'} className='rounded-full px-3'>
          {status ? 'Active' : 'Inactive'}
        </Tag>
      )
    },
    {
      title: 'Created At',
      dataIndex: 'created_at',
      key: 'created_at',
      render: date => dateClient(date)
    },
    {
      title: 'Actions',
      key: 'actions',
      align: 'center',
      render: record => (
        <Space>
          <Tooltip title='Edit User'>
            <Button
              type='text'
              icon={<EditOutlined className='text-blue-600 text-base' />}
              onClick={() => handleOpenModal(record)}
            />
          </Tooltip>
          <Tooltip title='Delete User'>
            <Button
              type='text'
              danger
              disabled={record.id === 1}
              icon={<DeleteOutlined className='text-base' />}
              onClick={() => handleDelete(record)}
            />
          </Tooltip>
        </Space>
      )
    }
  ];

  return (
    <div className='space-y-6'>
        {/* Header Title Card */}
        <div className='bg-gradient-to-r from-slate-900 to-indigo-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4'>
          <div>
            <h1 className='text-2xl font-bold tracking-tight text-white m-0 flex items-center gap-3'>
              <UserOutlined className='text-indigo-400' /> User Management
            </h1>
            <p className='text-slate-300 text-sm mt-1 mb-0'>
              Manage system accounts, employee user access, and assign security roles.
            </p>
          </div>
          <Button
            type='primary'
            size='large'
            icon={<PlusOutlined />}
            onClick={() => handleOpenModal()}
            className='bg-indigo-600 hover:bg-indigo-500 border-none rounded-xl font-semibold shadow-lg shadow-indigo-600/30'
          >
            Add New User
          </Button>
        </div>

        {/* Filter Controls */}
        <Card className='rounded-xl shadow-sm border-slate-100'>
          <Row gutter={[16, 16]} align='middle'>
            <Col xs={24} sm={12} md={8}>
              <Input
                placeholder='Search by name or email...'
                prefix={<SearchOutlined className='text-slate-400' />}
                allowClear
                value={filter.txt_search}
                onChange={e => setFilter(p => ({ ...p, txt_search: e.target.value }))}
                onPressEnter={getList}
              />
            </Col>
            <Col xs={12} sm={6} md={6}>
              <Select
                placeholder='Filter by Role'
                className='w-full'
                allowClear
                value={filter.role_id}
                onChange={val => setFilter(p => ({ ...p, role_id: val }))}
                options={state.roles.map(r => ({ label: r.name, value: r.id }))}
              />
            </Col>
            <Col xs={12} sm={6} md={4}>
              <Select
                placeholder='Status'
                className='w-full'
                allowClear
                value={filter.status}
                onChange={val => setFilter(p => ({ ...p, status: val }))}
                options={[
                  { label: 'Active', value: 1 },
                  { label: 'Inactive', value: 0 }
                ]}
              />
            </Col>
            <Col xs={24} md={6} className='flex justify-end gap-2'>
              <Button type='primary' onClick={getList} icon={<SearchOutlined />}>
                Filter
              </Button>
              <Button onClick={() => { setFilter({ txt_search: '', role_id: null, status: null }); setTimeout(getList, 10); }}>
                Reset
              </Button>
            </Col>
          </Row>
        </Card>

        {/* User Table */}
        <Card className='rounded-xl shadow-sm border-slate-100 overflow-hidden'>
          <Table
            dataSource={state.list}
            columns={columns}
            rowKey='id'
            loading={state.loading}
            pagination={{ pageSize: 10, showTotal: total => `Total ${total} users` }}
          />
        </Card>

        {/* Add/Edit Modal */}
        <Modal
          title={
            <span className='font-bold text-lg text-slate-800'>
              {state.editingUser ? 'Edit User Account' : 'Create New User Account'}
            </span>
          }
          open={state.open}
          onCancel={handleCloseModal}
          footer={null}
          centered
          width={600}
        >
          <Form form={form} layout='vertical' onFinish={onFinish} className='mt-4'>
            <Row gutter={16}>
              <Col span={12}>
                <Form.Item
                  label='Full Name'
                  name='name'
                  rules={[{ required: true, message: 'Please input user full name!' }]}
                >
                  <Input prefix={<UserOutlined />} placeholder='e.g. John Doe' />
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item
                  label='Email Address'
                  name='email'
                  rules={[
                    { required: true, message: 'Please input email!' },
                    { type: 'email', message: 'Invalid email address!' }
                  ]}
                >
                  <Input prefix={<MailOutlined />} placeholder='john@example.com' />
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item
                  label={state.editingUser ? 'New Password (Optional)' : 'Password'}
                  name='password'
                  rules={[{ required: !state.editingUser, message: 'Please input password!' }]}
                >
                  <Input.Password prefix={<LockOutlined />} placeholder='Min 6 characters' />
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item
                  label='Assigned Role'
                  name='role_id'
                  rules={[{ required: true, message: 'Please select a role!' }]}
                >
                  <Select
                    placeholder='Select Role'
                    options={state.roles.map(r => ({ label: r.name, value: r.id }))}
                  />
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item label='Phone Number' name='phone'>
                  <Input prefix={<PhoneOutlined />} placeholder='+855-12-345-678' />
                </Form.Item>
              </Col>

              <Col span={12}>
                <Form.Item label='Account Status' name='status' initialValue={1}>
                  <Select
                    options={[
                      { label: 'Active', value: 1 },
                      { label: 'Inactive', value: 0 }
                    ]}
                  />
                </Form.Item>
              </Col>

              <Col span={24}>
                <Form.Item label='Address' name='address'>
                  <Input.TextArea rows={2} placeholder='Enter address details...' />
                </Form.Item>
              </Col>
            </Row>

            <div className='flex justify-end gap-3 mt-4 border-t pt-4'>
              <Button onClick={handleCloseModal}>Cancel</Button>
              <Button type='primary' htmlType='submit' className='bg-indigo-600 border-none'>
                {state.editingUser ? 'Save Changes' : 'Create User'}
              </Button>
            </div>
          </Form>
        </Modal>
      </div>
  );
};

export default UserListPage;
