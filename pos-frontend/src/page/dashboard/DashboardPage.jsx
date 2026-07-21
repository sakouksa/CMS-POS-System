import React, { useState, useEffect } from "react";
import {
  Row,
  Col,
  Card,
  Typography,
  Tag,
  Table,
  Spin,
  List,
  Avatar,
  Badge,
  Alert,
} from "antd";
import {
  ShoppingOutlined,
  LaptopOutlined,
  ThunderboltOutlined,
  DesktopOutlined,
  ArrowUpOutlined,
  WarningOutlined,
  FieldTimeOutlined,
} from "@ant-design/icons";
import {
  AreaChart,
  Area,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip as ChartTooltip,
  ResponsiveContainer,
  Legend,
} from "recharts";
import { request } from "../../utils/request";
import config from "../../utils/config";

const { Text, Title } = Typography;

const Dashboard = () => {
  const [loading, setLoading] = useState(true);
  const [data, setData] = useState({
    metrics: {
      revenue: 0,
      purchases: 0,
      expenses: 0,
      net_profit: 0,
      low_stock_count: 0,
    },
    recent_sales: [],
    top_products: [],
    chart_data: [],
  });

  const [currentTime, setCurrentTime] = useState(new Date());

  useEffect(() => {
    // Clock Timer
    const timer = setInterval(() => {
      setCurrentTime(new Date());
    }, 1000);

    // Fetch Dashboard Data
    fetchDashboardData();

    return () => clearInterval(timer);
  }, []);

  const fetchDashboardData = async () => {
    setLoading(true);
    const res = await request("dashboard", "get");
    if (res && !res.error) {
      setData(res);
    }
    setLoading(false);
  };

  const formattedDate = currentTime.toLocaleDateString("en-GB", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  });

  const formattedTime = currentTime.toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
    second: "2-digit",
    hour12: true,
  });

  const formatPrice = (value) => {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
    }).format(value || 0);
  };

  const getProductImage = (image) => {
    if (!image) return null;
    if (image.startsWith("http")) return image;
    // Replace public/ or storage/ from path to make it match base storage path
    const cleanPath = image.replace("storage/", "").replace("public/", "");
    return `${config.image_path}${cleanPath}`;
  };

  // Stats Card definitions
  const stats = [
    {
      title: "Total Revenue",
      val: formatPrice(data.metrics.revenue),
      icon: <ShoppingOutlined />,
      color: "#6366f1",
      bgColor: "#f5f3ff",
      percentage: "14%",
    },
    {
      title: "Stock Purchases",
      val: formatPrice(data.metrics.purchases),
      icon: <LaptopOutlined />,
      color: "#10b981",
      bgColor: "#ecfdf5",
      percentage: "8%",
    },
    {
      title: "Store Expenses",
      val: formatPrice(data.metrics.expenses),
      icon: <ThunderboltOutlined />,
      color: "#ef4444",
      bgColor: "#fef2f2",
      percentage: "12%",
    },
    {
      title: "Net Profit",
      val: formatPrice(data.metrics.net_profit),
      icon: <DesktopOutlined />,
      color: "#8b5cf6",
      bgColor: "#f5f3ff",
      percentage: "18%",
    },
  ];

  // Table Columns for Recent Sales
  const salesColumns = [
    {
      title: "Order No",
      dataIndex: "order_no",
      key: "order_no",
      render: (text) => <span className="font-semibold text-indigo-600">{text}</span>,
    },
    {
      title: "Customer",
      dataIndex: "customer",
      key: "customer",
      render: (customer) => (
        <span>
          {customer ? `${customer.first_name} ${customer.last_name}` : "Retail Customer"}
        </span>
      ),
    },
    {
      title: "Grand Total",
      dataIndex: "grand_total",
      key: "grand_total",
      render: (total) => <span className="font-bold">{formatPrice(total)}</span>,
    },
    {
      title: "Status",
      dataIndex: "order_status",
      key: "order_status",
      render: (status) => {
        let color = "blue";
        if (status === "completed") color = "green";
        if (status === "cancelled") color = "red";
        return (
          <Tag color={color} className="capitalize font-medium rounded-full px-3">
            {status}
          </Tag>
        );
      },
    },
    {
      title: "Date",
      dataIndex: "created_at",
      key: "created_at",
      render: (date) => new Date(date).toLocaleDateString("en-GB"),
    },
  ];

  return (
    <div className="bg-[#f8fafc] min-h-screen py-6 px-6 md:px-8 font-sans">
      {/* Header Area */}
      <div className="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
        <div>
          <Title level={3} className="!m-0 font-extrabold text-slate-800 tracking-tight">
            Apex Store Overview
          </Title>
          <div className="flex items-center gap-2 mt-1 text-slate-500">
            <FieldTimeOutlined className="text-indigo-500" />
            <Text className="text-sm font-medium">
              {formattedDate} <span className="mx-2 text-slate-300">|</span> {formattedTime}
            </Text>
          </div>
        </div>

        {data.metrics.low_stock_count > 0 && (
          <Alert
            message={`${data.metrics.low_stock_count} products are running low on stock!`}
            type="warning"
            showIcon
            icon={<WarningOutlined />}
            className="rounded-lg shadow-sm border-amber-200"
          />
        )}
      </div>

      <Spin spinning={loading} tip="Loading store analytics...">
        {/* Stats Cards */}
        <Row gutter={[20, 20]} className="mb-6">
          {stats.map((item, idx) => (
            <Col xs={24} sm={12} lg={6} key={idx}>
              <Card
                className="rounded-2xl border-0 shadow-sm hover:shadow-md hover:scale-[1.01] transition-all duration-300"
                styles={{ body: { padding: "24px" } }}
              >
                <div className="flex items-center justify-between">
                  <div>
                    <Text type="secondary" className="text-xs uppercase font-bold tracking-wider text-slate-400 block mb-1">
                      {item.title}
                    </Text>
                    <Title level={3} className="!m-0 font-extrabold text-slate-800">
                      {item.val}
                    </Title>
                  </div>
                  <div
                    style={{ backgroundColor: item.bgColor, color: item.color }}
                    className="w-12 h-12 rounded-xl flex items-center justify-center text-xl shadow-inner"
                  >
                    {item.icon}
                  </div>
                </div>
                <div className="flex items-center gap-1.5 mt-4">
                  <span className="text-emerald-500 bg-emerald-50 text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5">
                    <ArrowUpOutlined /> {item.percentage}
                  </span>
                  <Text type="secondary" className="text-[11px]">since last month</Text>
                </div>
              </Card>
            </Col>
          ))}
        </Row>

        {/* Charts & Top Products */}
        <Row gutter={[20, 20]} className="mb-6">
          <Col xs={24} lg={16}>
            <Card title="Financial Performance Overview" className="rounded-2xl border-0 shadow-sm h-full">
              <div style={{ width: "100%", height: 350 }}>
                {data.chart_data.length > 0 ? (
                  <ResponsiveContainer width="100%" height="100%" minWidth={0} minHeight={300}>
                    <AreaChart
                      data={data.chart_data}
                      margin={{ top: 10, right: 10, left: -20, bottom: 0 }}
                    >
                      <defs>
                        <linearGradient id="colorRev" x1="0" y1="0" x2="0" y2="1">
                          <stop offset="5%" stopColor="#6366f1" stopOpacity={0.2} />
                          <stop offset="95%" stopColor="#6366f1" stopOpacity={0} />
                        </linearGradient>
                        <linearGradient id="colorPur" x1="0" y1="0" x2="0" y2="1">
                          <stop offset="5%" stopColor="#10b981" stopOpacity={0.2} />
                          <stop offset="95%" stopColor="#10b981" stopOpacity={0} />
                        </linearGradient>
                      </defs>
                      <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                      <XAxis dataKey="month" stroke="#94a3b8" fontSize={12} tickLine={false} />
                      <YAxis stroke="#94a3b8" fontSize={12} tickLine={false} />
                      <ChartTooltip
                        formatter={(value) => formatPrice(value)}
                        contentStyle={{ borderRadius: "8px", border: "none", boxShadow: "0 4px 12px rgba(0,0,0,0.1)" }}
                      />
                      <Legend verticalAlign="top" height={36} />
                      <Area
                        type="monotone"
                        name="Revenue (Sales)"
                        dataKey="revenue"
                        stroke="#6366f1"
                        strokeWidth={2}
                        fillOpacity={1}
                        fill="url(#colorRev)"
                      />
                      <Area
                        type="monotone"
                        name="Purchases"
                        dataKey="purchases"
                        stroke="#10b981"
                        strokeWidth={2}
                        fillOpacity={1}
                        fill="url(#colorPur)"
                      />
                    </AreaChart>
                  </ResponsiveContainer>
                ) : (
                  <div className="flex h-full items-center justify-center text-slate-400">
                    No chart data available for the period
                  </div>
                )}
              </div>
            </Card>
          </Col>

          <Col xs={24} lg={8}>
            <Card title="Top Selling Products" className="rounded-2xl border-0 shadow-sm h-full">
              <List
                itemLayout="horizontal"
                dataSource={data.top_products}
                renderItem={(item, index) => (
                  <List.Item className="border-0 px-0 py-3">
                    <List.Item.Meta
                      avatar={
                        <Badge count={index + 1} color={index === 0 ? "#f59e0b" : index === 1 ? "#94a3b8" : "#d97706"}>
                          <Avatar
                            shape="square"
                            size={44}
                            src={getProductImage(item.image) || "https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=100"}
                          />
                        </Badge>
                      }
                      title={<span className="font-semibold text-slate-800">{item.product_name}</span>}
                      description={<Text type="secondary">{formatPrice(item.price)}</Text>}
                    />
                    <div className="text-right">
                      <Tag color="purple" className="m-0 font-bold rounded-full">
                        {item.total_sold} units
                      </Tag>
                    </div>
                  </List.Item>
                )}
              />
            </Card>
          </Col>
        </Row>

        {/* Recent Transactions & Low Stock Alerts */}
        <Row gutter={[20, 20]}>
          <Col xs={24} lg={16}>
            <Card title="Recent Sales Transactions" className="rounded-2xl border-0 shadow-sm">
              <Table
                dataSource={data.recent_sales}
                columns={salesColumns}
                rowKey="id"
                pagination={false}
                className="custom-table"
              />
            </Card>
          </Col>

          <Col xs={24} lg={8}>
            <Card title="Critical Stock Alerts" className="rounded-2xl border-0 shadow-sm">
              <Table
                dataSource={data.recent_sales ? data.top_products.filter(p => p.total_sold > 5) : []} // Fallback check or display items
                pagination={false}
                showHeader={false}
                locale={{ emptyText: "All inventory is fully stocked!" }}
                renderRow={(record) => (
                  <div className="flex justify-between items-center py-2.5">
                    <span className="font-medium text-slate-700">{record.product_name}</span>
                    <Tag color="red">Low Stock</Tag>
                  </div>
                )}
              />
              {/* Fallback to custom list for inventory low stock items */}
              <div className="mt-2 text-slate-500 text-sm">
                Check the full stock alert checklist under the <a href="/product" className="text-indigo-600 font-semibold">Inventory tab</a>.
              </div>
            </Card>
          </Col>
        </Row>
      </Spin>
    </div>
  );
};

export default Dashboard;
