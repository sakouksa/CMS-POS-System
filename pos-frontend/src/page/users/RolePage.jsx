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
  Checkbox,
  Row,
  Col,
  Tooltip,
  Divider,
  Badge
} from 'antd';
import {
  SafetyCertificateOutlined,
  SearchOutlined,
  PlusOutlined,
  EditOutlined,
  DeleteOutlined,
  ExclamationCircleFilled,
  LockOutlined,
  CheckCircleOutlined
} from '@ant-design/icons';
import { request } from '../../utils/request';
import { dateClient } from '../../utils/helper';
import MainPage from '../../component/common/PageLoader';

const RolePage = () => {
  const [formRef] = Form.useForm();
  const [state, setState] = useState({
    list: [],
    allPermissions: [],
    total: 0,
    loading: false,
    open: false,
    permissionModalOpen: false,
    selectedRole: null,
    editingRole: null
  });

  const [selectedPermissions, setSelectedPermissions] = useState([]);
  const [filter, setFilter] = useState({
    text_search: '',
    status: ''
  });

  useEffect(() => {
    getlist();
  }, []);

  const getlist = async () => {
    setState(pre => ({ ...pre, loading: true }));
    let query_param = '?page=1';
    if (filter.text_search) {
      query_param += '&text_search=' + encodeURIComponent(filter.text_search);
    }
    if (filter.status !== null && filter.status !== '') {
      query_param += '&status=' + filter.status;
    }

    const res = await request('role' + query_param, 'get');
    if (res && !res.errors) {
      setState(pre => ({
        ...pre,
        total: res.list?.length || 0,
        list: res.list || [],
        allPermissions: res.permissions || [],
        loading: false
      }));
    } else {
      setState(pre => ({ ...pre, loading: false }));
      message.error(res?.message || 'Failed to fetch roles');
    }
  };

  const handleOpenModal = (role = null) => {
    setState(pre => ({ ...pre, open: true, editingRole: role }));
    if (role) {
      formRef.setFieldsValue({
        id: role.id,
        name: role.name,
        code: role.code,
        description: role.description,
        status: role.status ?? 1
      });
    } else {
      formRef.resetFields();
      formRef.setFieldsValue({ status: 1 });
    }
  };

  const handleCloseModal = () => {
    setState(pre => ({ ...pre, open: false, editingRole: null }));
    formRef.resetFields();
  };

  const handleOpenPermissionModal = role => {
    const rolePermissionIds = (role.permissions || []).map(p => p.id);
    setSelectedPermissions(rolePermissionIds);
    setState(pre => ({
      ...pre,
      permissionModalOpen: true,
      selectedRole: role
    }));
  };

  const handleClosePermissionModal = () => {
    setState(pre => ({ ...pre, permissionModalOpen: false, selectedRole: null }));
    setSelectedPermissions([]);
  };

  const onFinish = async values => {
    const isEdit = !!formRef.getFieldValue('id');
    const url = isEdit ? `role/${formRef.getFieldValue('id')}` : 'role';
    const method = isEdit ? 'put' : 'post';

    const res = await request(url, method, values);
    if (res && !res.errors) {
      message.success(res.message || 'Saved successfully!');
      handleCloseModal();
      getlist();
    } else {
      message.error(res?.message || 'Operation failed!');
    }
  };

  const handleSavePermissions = async () => {
    if (!state.selectedRole) return;
    const url = `role/${state.selectedRole.id}`;
    const payload = {
      name: state.selectedRole.name,
      code: state.selectedRole.code,
      description: state.selectedRole.description,
      status: state.selectedRole.status,
      permissions: selectedPermissions
    };

    const res = await request(url, 'put', payload);
    if (res && !res.errors) {
      message.success('Role permissions updated successfully!');
      handleClosePermissionModal();
      getlist();
    } else {
      message.error(res?.message || 'Failed to update permissions!');
    }
  };

  const handleDelete = data => {
    Modal.confirm({
      title: 'Confirm Delete Role',
      icon: <ExclamationCircleFilled style={{ color: '#ff4d4f' }} />,
      content: `Are you sure you want to delete role "${data.name}"?`,
      okText: 'Delete',
      okType: 'danger',
      cancelText: 'Cancel',
      centered: true,
      onOk: async () => {
        const res = await request(`role/${data.id}`, 'delete');
        if (res && !res.errors) {
          message.success(res.message || 'Deleted successfully!');
          getlist();
        } else {
          message.error(res?.message || 'Failed to delete role!');
        }
      }
    });
  };

  // Group permissions by module
  const groupedPermissions = state.allPermissions.reduce((acc, item) => {
    const group = item.group || 'General';
    if (!acc[group]) acc[group] = [];
    acc[group].push(item);
    return acc;
  }, {});

  const columns = [
    {
      title: 'Role Name',
      dataIndex: 'name',
      key: 'name',
      render: (name, record) => (
        <div>
          <div className='font-bold text-slate-800 text-sm'>{name}</div>
          <div className='text-xs text-slate-400 font-mono'>{record.code}</div>
        </div>
      )
    },
    {
      title: 'Description',
      dataIndex: 'description',
      key: 'description',
      render: desc => desc || <span className='text-slate-300'>No description</span>
    },
    {
      title: 'Assigned Permissions',
      key: 'permissions',
      render: record => {
        const count = record.permissions ? record.permissions.length : 0;
        return (
          <Badge
            count={`${count} Permissions`}
            style={{ backgroundColor: count > 0 ? '#52c41a' : '#d9d9d9', color: '#fff' }}
          />
        );
      }
    },
    {
      title: 'Status',
      dataIndex: 'status',
      key: 'status',
      align: 'center',
      render: status => (
        <Tag color={status ? 'green' : 'red'} className='rounded-full px-3'>
          {status ? 'Active' : 'Inactive'}
        </Tag>
      )
    },
    {
      title: 'Created Date',
      dataIndex: 'created_at',
      key: 'created_at',
      render: date => dateClient(date)
    },
    {
      title: 'Actions',
      key: 'action',
      align: 'center',
      render: record => (
        <Space>
          <Tooltip title='Manage Permissions'>
            <Button
              type='primary'
              ghost
              size='small'
              icon={<SafetyCertificateOutlined />}
              onClick={() => handleOpenPermissionModal(record)}
            >
              Permissions
            </Button>
          </Tooltip>
          <Tooltip title='Edit Role'>
            <Button
              type='text'
              icon={<EditOutlined className='text-blue-600 text-base' />}
              onClick={() => handleOpenModal(record)}
            />
          </Tooltip>
          <Tooltip title='Delete Role'>
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
        {/* Header Title */}
        <div className='bg-gradient-to-r from-purple-900 to-indigo-900 rounded-2xl p-6 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4'>
          <div>
            <h1 className='text-2xl font-bold tracking-tight text-white m-0 flex items-center gap-3'>
              <SafetyCertificateOutlined className='text-purple-400' /> Role & Access Management
            </h1>
            <p className='text-slate-300 text-sm mt-1 mb-0'>
              Define security roles, configure granular permissions, and control user module access.
            </p>
          </div>
          <Button
            type='primary'
            size='large'
            icon={<PlusOutlined />}
            onClick={() => handleOpenModal()}
            className='bg-purple-600 hover:bg-purple-500 border-none rounded-xl font-semibold shadow-lg shadow-purple-600/30'
          >
            Create New Role
          </Button>
        </div>

        {/* Filter Card */}
        <Card className='rounded-xl shadow-sm border-slate-100'>
          <Space wrap className='w-full justify-between'>
            <Space wrap>
              <Input
                placeholder='Search role name...'
                prefix={<SearchOutlined className='text-slate-400' />}
                allowClear
                value={filter.text_search}
                onChange={e => setFilter(p => ({ ...p, text_search: e.target.value }))}
                onPressEnter={getlist}
                className='w-64'
              />
              <Select
                placeholder='Status'
                className='w-36'
                allowClear
                value={filter.status}
                onChange={val => setFilter(p => ({ ...p, status: val }))}
                options={[
                  { label: 'Active', value: 1 },
                  { label: 'Inactive', value: 0 }
                ]}
              />
              <Button type='primary' onClick={getlist} icon={<SearchOutlined />}>
                Filter
              </Button>
            </Space>
            <div className='text-slate-500 text-sm font-semibold'>
              Total Roles: <span className='text-indigo-600'>{state.list.length}</span>
            </div>
          </Space>
        </Card>

        {/* Role Table */}
        <Card className='rounded-xl shadow-sm border-slate-100 overflow-hidden'>
          <Table dataSource={state.list} columns={columns} rowKey='id' loading={state.loading} pagination={false} />
        </Card>

        {/* Create/Edit Role Modal */}
        <Modal
          title={
            <span className='font-bold text-lg text-slate-800'>
              {state.editingRole ? 'Edit Role' : 'Create New Role'}
            </span>
          }
          open={state.open}
          onCancel={handleCloseModal}
          footer={null}
          centered
          width={500}
        >
          <Form layout='vertical' onFinish={onFinish} form={formRef} className='mt-4'>
            <Form.Item name='id' hidden>
              <Input />
            </Form.Item>
            <Form.Item
              label='Role Name'
              name='name'
              rules={[{ required: true, message: 'Please input role name!' }]}
            >
              <Input placeholder='e.g. Store Manager' />
            </Form.Item>

            <Form.Item
              label='Role Code'
              name='code'
              rules={[{ required: true, message: 'Please input role code!' }]}
            >
              <Input placeholder='e.g. store_manager' />
            </Form.Item>

            <Form.Item label='Description' name='description'>
              <Input.TextArea rows={3} placeholder='Role responsibilities description...' />
            </Form.Item>

            <Form.Item label='Status' name='status' initialValue={1}>
              <Select
                options={[
                  { label: 'Active', value: 1 },
                  { label: 'Inactive', value: 0 }
                ]}
              />
            </Form.Item>

            <div className='flex justify-end gap-3 mt-4 border-t pt-4'>
              <Button onClick={handleCloseModal}>Cancel</Button>
              <Button type='primary' htmlType='submit' className='bg-purple-600 border-none'>
                Save Role
              </Button>
            </div>
          </Form>
        </Modal>

        {/* Role-Permission Matrix Modal */}
        <Modal
          title={
            <div className='flex items-center gap-2 text-slate-800 font-bold text-lg'>
              <LockOutlined className='text-purple-600' />
              <span>Permission Settings for Role:</span>
              <Tag color='purple' className='text-sm px-3 py-1 font-bold'>
                {state.selectedRole?.name}
              </Tag>
            </div>
          }
          open={state.permissionModalOpen}
          onCancel={handleClosePermissionModal}
          width={800}
          centered
          onOk={handleSavePermissions}
          okText='Save Permissions'
          okButtonProps={{ className: 'bg-purple-600 border-none' }}
        >
          <div className='max-h-[60vh] overflow-y-auto pr-2 mt-4 space-y-6'>
            {Object.keys(groupedPermissions).map(group => {
              const perms = groupedPermissions[group];
              const allGroupChecked = perms.every(p => selectedPermissions.includes(p.id));

              return (
                <div key={group} className='bg-slate-50 p-4 rounded-xl border border-slate-200'>
                  <div className='flex justify-between items-center mb-3 pb-2 border-b border-slate-200'>
                    <h3 className='font-bold text-slate-700 m-0 uppercase tracking-wider text-xs flex items-center gap-2'>
                      <CheckCircleOutlined className='text-indigo-500' /> {group} Module
                    </h3>
                    <Checkbox
                      checked={allGroupChecked}
                      onChange={e => {
                        const groupIds = perms.map(p => p.id);
                        if (e.target.checked) {
                          setSelectedPermissions(prev => Array.from(new Set([...prev, ...groupIds])));
                        } else {
                          setSelectedPermissions(prev => prev.filter(id => !groupIds.includes(id)));
                        }
                      }}
                      className='text-xs font-semibold text-slate-500'
                    >
                      Select All {group}
                    </Checkbox>
                  </div>
                  <Row gutter={[12, 12]}>
                    {perms.map(p => (
                      <Col span={12} md={8} key={p.id}>
                        <Checkbox
                          checked={selectedPermissions.includes(p.id)}
                          onChange={e => {
                            if (e.target.checked) {
                              setSelectedPermissions(prev => [...prev, p.id]);
                            } else {
                              setSelectedPermissions(prev => prev.filter(id => id !== p.id));
                            }
                          }}
                          className='text-xs text-slate-700'
                        >
                          {p.name}
                        </Checkbox>
                      </Col>
                    ))}
                  </Row>
                </div>
              );
            })}
          </div>
        </Modal>
      </div>
  );
};

export default RolePage;
