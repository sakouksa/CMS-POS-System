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
  Row,
  Col,
  Tooltip,
  Switch
} from 'antd';
import {
  KeyOutlined,
  SearchOutlined,
  PlusOutlined,
  EditOutlined,
  DeleteOutlined,
  ExclamationCircleFilled,
  AppstoreOutlined
} from '@ant-design/icons';
import { request } from '../../utils/request';
import MainPage from '../../component/common/PageLoader';

const GroupColors = {
  Product: 'blue',
  Category: 'cyan',
  Brand: 'purple',
  Customer: 'green',
  Employee: 'magenta',
  Order: 'gold',
  Purchase: 'orange',
  Expense: 'volcano',
  Role: 'geekblue',
  User: 'red',
  Settings: 'lime'
};

const PermissionPage = () => {
  const [form] = Form.useForm();
  const [state, setState] = useState({
    list: [],
    groups: [],
    total: 0,
    loading: false,
    open: false,
    editingPermission: null
  });

  const [filter, setFilter] = useState({
    txt_search: '',
    group: null
  });

  useEffect(() => {
    getList();
  }, []);

  const getList = async () => {
    setState(prev => ({ ...prev, loading: true }));
    let query = '?page=1';
    if (filter.txt_search) query += `&txt_search=${encodeURIComponent(filter.txt_search)}`;
    if (filter.group) query += `&group=${encodeURIComponent(filter.group)}`;

    const res = await request(`permissions${query}`, 'get');
    if (res && !res.errors) {
      setState(prev => ({
        ...prev,
        list: res.list || [],
        total: res.total || 0,
        groups: res.groups || [],
        loading: false
      }));
    } else {
      setState(prev => ({ ...prev, loading: false }));
      message.error(res?.message || 'Failed to fetch permissions');
    }
  };

  const handleOpenModal = (permission = null) => {
    setState(prev => ({ ...prev, open: true, editingPermission: permission }));
    if (permission) {
      form.setFieldsValue({
        id: permission.id,
        name: permission.name,
        group: permission.group,
        is_menu_web: permission.is_menu_web ?? false,
        web_route_key: permission.web_route_key || ''
      });
    } else {
      form.resetFields();
      form.setFieldsValue({ is_menu_web: false });
    }
  };

  const handleCloseModal = () => {
    setState(prev => ({ ...prev, open: false, editingPermission: null }));
    form.resetFields();
  };

  const onFinish = async values => {
    const isEdit = !!state.editingPermission;
    const url = isEdit ? `permissions/${state.editingPermission.id}` : 'permissions';
    const method = isEdit ? 'put' : 'post';

    const res = await request(url, method, values);
    if (res && !res.errors) {
      message.success(res.message || (isEdit ? 'Permission updated successfully!' : 'Permission created successfully!'));
      handleCloseModal();
      getList();
    } else {
      message.error(res?.message || 'Operation failed!');
    }
  };

  const handleDelete = permission => {
    Modal.confirm({
      title: 'Confirm Delete Permission',
      icon: <ExclamationCircleFilled style={{ color: '#ff4d4f' }} />,
      content: `Are you sure you want to delete permission "${permission.name}"?`,
      okText: 'Yes, Delete',
      okType: 'danger',
      cancelText: 'Cancel',
      centered: true,
      onOk: async () => {
        const res = await request(`permissions/${permission.id}`, 'delete');
        if (res && !res.errors) {
          message.success(res.message || 'Permission deleted successfully!');
          getList();
        } else {
          message.error(res?.message || 'Failed to delete permission!');
        }
      }
    });
  };

  const columns = [
    {
      title: 'Permission Code',
      dataIndex: 'name',
      key: 'name',
      render: text => <span className='font-mono font-bold text-slate-800'>{text}</span>
    },
    {
      title: 'Module Group',
      dataIndex: 'group',
      key: 'group',
      render: group => {
        const color = GroupColors[group] || 'blue';
        return <Tag color={color} className='font-semibold px-3 py-0.5 rounded-full'>{group}</Tag>;
      }
    },
    {
      title: 'Is Web Menu',
      dataIndex: 'is_menu_web',
      key: 'is_menu_web',
      align: 'center',
      render: isMenu => (
        <Tag color={isMenu ? 'success' : 'default'} className='rounded-full px-3'>
          {isMenu ? 'Yes' : 'No'}
        </Tag>
      )
    },
    {
      title: 'Route Key',
      dataIndex: 'web_route_key',
      key: 'web_route_key',
      render: key => key ? <span className='font-mono text-xs text-indigo-600'>{key}</span> : <span className='text-slate-300'>N/A</span>
    },
    {
      title: 'Actions',
      key: 'actions',
      align: 'center',
      render: record => (
        <Space>
          <Tooltip title='Edit Permission'>
            <Button
              type='text'
              icon={<EditOutlined className='text-blue-600 text-base' />}
              onClick={() => handleOpenModal(record)}
            />
          </Tooltip>
          <Tooltip title='Delete Permission'>
            <Button
              type='text'
              danger
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
        {/* Header Banner */}
        <div className='bg-gradient-to-r from-cyan-900 to-blue-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4'>
          <div>
            <h1 className='text-2xl font-bold tracking-tight text-white m-0 flex items-center gap-3'>
              <KeyOutlined className='text-cyan-400' /> Permission Registry
            </h1>
            <p className='text-slate-300 text-sm mt-1 mb-0'>
              Register and manage backend API security permissions and frontend navigation keys.
            </p>
          </div>
          <Button
            type='primary'
            size='large'
            icon={<PlusOutlined />}
            onClick={() => handleOpenModal()}
            className='bg-cyan-600 hover:bg-cyan-500 border-none rounded-xl font-semibold shadow-lg shadow-cyan-600/30'
          >
            Create Permission
          </Button>
        </div>

        {/* Filter Cards */}
        <Card className='rounded-xl shadow-sm border-slate-100'>
          <Row gutter={[16, 16]} align='middle'>
            <Col xs={24} sm={12} md={8}>
              <Input
                placeholder='Search permission code or group...'
                prefix={<SearchOutlined className='text-slate-400' />}
                allowClear
                value={filter.txt_search}
                onChange={e => setFilter(p => ({ ...p, txt_search: e.target.value }))}
                onPressEnter={getList}
              />
            </Col>
            <Col xs={12} sm={6} md={6}>
              <Select
                placeholder='Filter by Module Group'
                className='w-full'
                allowClear
                value={filter.group}
                onChange={val => setFilter(p => ({ ...p, group: val }))}
                options={state.groups.map(g => ({ label: g, value: g }))}
              />
            </Col>
            <Col xs={24} md={10} className='flex justify-end gap-2'>
              <Button type='primary' onClick={getList} icon={<SearchOutlined />}>
                Filter
              </Button>
              <Button onClick={() => { setFilter({ txt_search: '', group: null }); setTimeout(getList, 10); }}>
                Reset
              </Button>
            </Col>
          </Row>
        </Card>

        {/* Table */}
        <Card className='rounded-xl shadow-sm border-slate-100 overflow-hidden'>
          <Table
            dataSource={state.list}
            columns={columns}
            rowKey='id'
            loading={state.loading}
            pagination={{ pageSize: 15, showTotal: total => `Total ${total} permissions` }}
          />
        </Card>

        {/* Create/Edit Modal */}
        <Modal
          title={
            <span className='font-bold text-lg text-slate-800'>
              {state.editingPermission ? 'Edit Permission' : 'Create New Permission'}
            </span>
          }
          open={state.open}
          onCancel={handleCloseModal}
          footer={null}
          centered
          width={500}
        >
          <Form form={form} layout='vertical' onFinish={onFinish} className='mt-4'>
            <Form.Item
              label='Permission Code'
              name='name'
              rules={[{ required: true, message: 'Please input permission code!' }]}
            >
              <Input placeholder='e.g. product.create' />
            </Form.Item>

            <Form.Item
              label='Module Group'
              name='group'
              rules={[{ required: true, message: 'Please input module group!' }]}
            >
              <Input placeholder='e.g. Product, Customer, Order' />
            </Form.Item>

            <Form.Item label='Is Menu Web' name='is_menu_web' valuePropName='checked'>
              <Switch />
            </Form.Item>

            <Form.Item label='Web Route Key' name='web_route_key'>
              <Input placeholder='e.g. product, customer, pos' />
            </Form.Item>

            <div className='flex justify-end gap-3 mt-4 border-t pt-4'>
              <Button onClick={handleCloseModal}>Cancel</Button>
              <Button type='primary' htmlType='submit' className='bg-cyan-600 border-none'>
                {state.editingPermission ? 'Save Changes' : 'Create Permission'}
              </Button>
            </div>
          </Form>
        </Modal>
      </div>
  );
};

export default PermissionPage;
