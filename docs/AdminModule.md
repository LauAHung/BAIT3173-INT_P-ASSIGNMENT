## Admin Module — Feature Overview, Flow, Controllers, Services, Middleware, and APIs

This document explains the Admin Module end-to-end: what the dashboard shows, how each feature works, which controllers/services/middleware are involved, and the relevant routes and APIs.

### 1. High-level Architecture
- **Admin UI (web routes under `/admin`)**: Blade views rendered by `App\Http\Controllers\AdminController` and Admin namespace controllers.
- **Admin AJAX/WebService APIs (under `/api/admin`)**: JSON endpoints used by the Admin UI and integrations. Implemented by `AdminController` (AJAX), `App\Http\Controllers\Api\AdminWebServiceController`, and Admin namespace API controllers.
- **Facade**: `App\Facades\AdminFacade` provides a unified interface delegating to services.
- **Services**: Orchestrate business logic, e.g., `AdminService`, `AdminModuleService`, `UserService`, `TrainService`, `QRScannerService`, `NewsletterService`, `RefundService`.
- **Security**: Middleware `admin` (role check), `admin.2fa` (enforce 2FA), `admin.recent` (reauth for sensitive ops), plus auth session.
- **Auditing**: `AdminActivityLogger` persists records into `admin_activity_logs` via `AdminActivityLog` model.

### 2. Access Control & Security Flow
- Login required: `auth` middleware in `AdminController::__construct()`.
- Admin role required: `admin` middleware used in `routes/web.php` and `routes/api.php` admin groups.
- 2FA enforcement: `App\Http\Middleware\AdminTwoFactor` (`admin.2fa`), redirects to `admin.2fa.setup` or `admin.2fa.challenge` if not satisfied.
- Recent re-auth for sensitive actions: `App\Http\Middleware\AdminRecentReauth` (`admin.recent`) requires a recent session timestamp.
- 2FA generation/verification powered by `App\Services\TwoFactorService` (TOTP-based helper).

### 3. Main Screens and Features
1) Dashboard (`GET /admin/dashboard`)
   - Shows overall system statistics pulled from `AdminService::getDashboardStats()` via `AdminFacade::getDashboardStats()`.
   - AJAX datasets for charts/filters:
     - `GET /api/admin/dashboard/stats` → `AdminController::getDashboardStats()`
     - `GET /api/admin/dashboard/filters` → States/Stations filters
     - `GET /api/admin/dashboard/trips` → Trips per month (filterable)
     - `GET /api/admin/dashboard/users-growth` → Registered users growth per month
     - `GET /api/admin/dashboard/profit` → Profit trends per month

2) User Management (`GET /admin/users` or `GET /user-management`)
   - Page data via `AdminController::users()` or `Admin\UserController::index()`.
   - Admin actions:
     - Update status: `PUT /api/admin/users/{userId}/status` → `Admin\UserController::updateStatus()`
     - Delete user: `DELETE /api/admin/users/{userId}` → `Admin\UserController::destroy()`
     - Export CSV: `GET /api/admin/users/export` → `Admin\UserController::export()` (throttled and session-limited)
   - Data sources: `UserService` for listing/stats; `AdminActivityLogger` logs promotions, deletions, etc.

3) Train Management (`GET /admin/trains` and `GET /train-management`)
   - Reads/updates trains, stations, journeys via `Admin\TrainManagementController`.
   - Creation/update endpoints (web):
     - `POST admin/train-management/train` → `storeTrain()` → `AdminFacade::addTrain()` → `AdminModuleService::createTrain()`
     - `POST admin/train-management/station` → `storeStation()` → `AdminFacade::addStation()`
     - `POST admin/train-management/journey` → `storeJourney()` → `AdminFacade::addJourney()`
     - `POST admin/train-management/train/update` → `updateTrain()` → `AdminFacade::updateTrain()`
     - `POST admin/train-management/station/update` → `updateStation()`
     - `POST admin/train-management/journey/update` → `updateJourney()`
   - Admin AJAX (API group):
     - `GET /api/admin/trains` | `POST /api/admin/trains` | `PUT /api/admin/trains/{id}` | `DELETE /api/admin/trains/{id}` → `AdminController`
   - Logic: `AdminModuleService` validates and persists to `Trains`, `Stations`, `Journeys` tables and logs actions.

4) QR Scanner (`GET /admin/qr-scanner`)
   - AJAX:
     - `POST /api/admin/qr/scan` → `AdminController::scanQR()` → `AdminFacade::scanQR()` → `QRScannerService::scanQR()`
     - `POST /api/admin/qr/generate` → `AdminController::generateQR()` → `AdminFacade::generateQR()`
   - QR service handles base64 JSON QR encode/decode and basic checks.

5) Newsletter (`GET /admin/newsletter`)
   - Stats and sending:
     - `GET /api/admin/newsletter/subscribers` → `NewsletterController::list`
     - `POST /api/admin/newsletter/unsubscribe` → `NewsletterController::unsubscribe`
     - `POST /api/admin/newsletter/send` → `AdminController::sendNewsletter()` → `AdminFacade::sendNewsletter()` → `NewsletterService::sendNewsletter()`

6) Refund Management (`GET /admin/refunds`)
   - `POST /api/admin/refunds/process` → `AdminController::processRefund()` → `AdminFacade::processRefund()`
   - `RefundService` contains placeholder processing logic and stats endpoints.

7) System Info (`GET /admin/system-info`)
   - `GET /api/admin/system/info` → `AdminController::getSystemInfo()` → `AdminFacade::getSystemInfo()` → `AdminService::getSystemInfo()`

8) Tickets QR Flows (Check-in/Check-out)
   - `GET /api/admin/tickets/{ticketId}` → `Admin\TicketController::show()`
   - `POST /api/admin/tickets/{ticketId}/checkin` → `Admin\TicketController::checkIn()`
   - `POST /api/admin/tickets/{ticketId}/checkout` → `Admin\TicketController::checkOut()`
   - Validates ticket status transitions and logs via `AdminActivityLogger`.

### 4. Controllers
- `App\Http\Controllers\AdminController`
  - Pages: `dashboard`, `users`, `trains`, `qrScanner`, `newsletter`, `refunds`, `systemInfo`
  - AJAX: `getDashboardStats`, `getDashboardFilters`, `getTripsPerMonth`, `getUsersGrowth`, `getProfitTrends`, `getUsers`, `updateUserStatus`, `deleteUser`, `getTrains`, `addTrain`, `updateTrain`, `deleteTrain`, `scanQR`, `generateQR`, `sendNewsletter`, `processRefund`, `exportData`, `downloadExport`, `getSystemInfo`

- `App\Http\Controllers\Admin\UserController`
  - `index`, `updateStatus`, `destroy`, `export` (rate-limited in-session, plus route throttle on `/api/admin/users/export`)

- `App\Http\Controllers\Admin\TrainManagementController`
  - `index` (Blade with trains/stations/journeys), CRUD helpers for train/station/journey via Facade/Service

- `App\Http\Controllers\Admin\TicketController`
  - Ticket retrieval and QR-driven state transitions (check-in/out)

- `App\Http\Controllers\Admin\LogController`
  - `list` returns `admin_activity_logs` with optional filters

- `App\Http\Controllers\Api\AdminWebServiceController`
  - Partner-friendly JSON endpoints: user info, train info, logs, newsletter subscribers, concession decision, etc.

### 5. Services & Facade
- `App\Facades\AdminFacade` delegates to services:
  - `AdminService`: dashboard stats, system info, exports, DB/cache helpers
  - `UserService`: user listing, status updates, exports, statistics
  - `TrainService`: placeholder list/update for API-backed parts
  - `AdminModuleService`: authoritative CRUD for `Trains`, `Stations`, `Journeys`; also admin user-status updates
  - `QRScannerService`: QR encode/decode, validate, basic check-in/out helper
  - `NewsletterService`: recipients aggregation and mail send
  - `RefundService`: refund submission and stats (placeholder)
- `AdminActivityLogger`: writes to `AdminActivityLog` model; never blocks on failure.

### 6. Middleware
- `admin` (custom, not shown here but used in routes): enforces user is admin (`account_status === 'admin'`).
- `App\Http\Middleware\AdminTwoFactor` (`admin.2fa`): forces 2FA setup/challenge for admins before accessing admin routes.
- `App\Http\Middleware\AdminRecentReauth` (`admin.recent`): requires recent re-auth for sensitive endpoints; used with route throttling.

### 7. Routes
- `routes/web.php`
  - Admin group: `Route::prefix('admin')->middleware(['admin','admin.2fa'])`
    - `GET /admin/dashboard`, `GET /admin/users`, `GET /admin/trains`, `GET /admin/qr-scanner`, `GET /admin/newsletter`, `GET /admin/refunds`, `GET /admin/system-info`
  - Admin 2FA setup/challenge routes under `admin` + `admin` middleware.
  - Entry point `/admin` redirects to dashboard if admin, else 403.
  - Train and user management pages and posting endpoints via `Admin\TrainManagementController`, `Admin\UserController`.

- `routes/api.php`
  - `Route::prefix('admin')->middleware(['web','admin','admin.2fa'])` for Admin Web Services and Admin AJAX endpoints:
    - Dashboard data: `GET /dashboard/stats|filters|trips|users-growth|profit`
    - Trains CRUD: `GET|POST|PUT|DELETE /trains`
    - QR: `POST /qr/scan`, `POST /qr/generate`
    - Tickets: `GET /tickets/{ticketId}`, `POST /tickets/{ticketId}/checkin|checkout`
    - Users: `GET /users`, `PUT /users/{userId}/status`, `DELETE /users/{userId}`, `GET /users/export` (throttle + `admin.recent`)
    - System: `GET /system/info`, Logs: `GET /logs`
    - Newsletter: `GET /newsletter/subscribers`, `POST /newsletter/unsubscribe`, `POST /newsletter/send`
    - Concession: `POST /concession/decision`
    - Journey provider (for Booking): `GET /journey/{journeyId}` → `Api\AdminWebServiceController@getJourneyById`

### 8. Data and Models
- `AdminActivityLog` (table `admin_activity_logs`): `admin_email`, `action`, `details` (JSON), timestamps.
- Core entities used by admin features: `User`, `Train`, `Station`, `Journey`, `Booking`, `Ticket`, and `NewsletterSubscriber`.

### 9. Dashboard Metrics (via `AdminService`)
- Users: total, active, pending verification, social sign-ins.
- Trains: total, active.
- Bookings: total; recent users/bookings lists.
- Refunds pending count (if `refunds` table exists).

### 10. Auditing
- All critical admin actions call `AdminActivityLogger::log(action, details)`.
- Logs are visible via `Admin\LogController::list` and API `GET /api/admin/logs`.

### 11. Notes & Gotchas
- Some services (e.g., `TrainService`, `RefundService`) contain placeholder logic for demo/dev. Use `AdminModuleService` for authoritative train/station/journey CRUD.
- Sensitive routes add `throttle` and `admin.recent` middleware for extra protection.
- Admin 2FA must be enabled before accessing most admin routes; promotion to admin resets 2FA to force setup on next login.



### 12. 中文详细说明（功能、接口、校验与流程）

#### 12.1 模块概览（中文）
- 管理端入口：`/admin`，若用户为管理员（`account_status === 'admin'`）则跳转到 Dashboard，否则 403。
- 安全中间件：`admin`（管理员角色）、`admin.2fa`（双重验证）、`admin.recent`（敏感操作短期再认证），搭配 `throttle` 限流。
- 统一门面：`AdminFacade` 将控制器的调用解耦到服务层（如用户、火车/车站/行程、QR、Newsletter、退款、系统信息等）。
- 审计：所有关键动作通过 `AdminActivityLogger` 记录到 `admin_activity_logs` 表（JSON details）。

#### 12.2 Dashboard（仪表盘）
- 页面：`GET /admin/dashboard`（`AdminController::dashboard`）
- 数据源：`AdminService::getDashboardStats()`，缓存键 `ads`（5 分钟），字段包含：
  - `total_users`、`active_users`、`pending_users`、`social_users`
  - `total_trains`、`active_trains`
  - `total_bookings`、`pending_refunds`
  - `recent_users[5]`、`recent_bookings[5]`
- 前端图表 AJAX：

| 方法 | 路径 | 控制器方法 | 说明 |
| - | - | - | - |
| GET | `/api/admin/dashboard/stats` | `AdminController@getDashboardStats` | 拉取综合指标 |
| GET | `/api/admin/dashboard/filters` | `AdminController@getDashboardFilters` | 拉取州/车站过滤器 |
| GET | `/api/admin/dashboard/trips?state&station` | `AdminController@getTripsPerMonth` | 每月出行量（可选筛选）|
| GET | `/api/admin/dashboard/users-growth` | `AdminController@getUsersGrowth` | 用户增长（按月）|
| GET | `/api/admin/dashboard/profit` | `AdminController@getProfitTrends` | 利润趋势（按月）|

返回通用结构（示例）：
```json
{ "success": true, "data": [...] }
```

#### 12.3 用户管理
- 页面：`GET /admin/users` 或 `GET /user-management`
- 列表/统计：
  - `AdminController::users()` 内部使用 `UserService::getUsers(page, perPage, search)`。
  - 或 `Admin\UserController::index()` 直接 Eloquent + 筛选（`search`, `status`）。
- 操作接口（API 组均走 `['web','admin','admin.2fa']` 中间件）：

| 方法 | 路径 | 控制器方法 | 说明 | 校验/限制 |
| - | - | - | - | - |
| GET | `/api/admin/users` | `AdminController@getUsers` | 代理到后端用户列表（内部再调 `${api_base_url}/user/list`）| query: `page,search,status,role` |
| PUT | `/api/admin/users/{userId}/status` | `Admin\UserController@updateStatus` | 更新用户状态 | body: `status in [active,suspended,not_verified,admin]`；如升为 admin，强制重置 2FA |
| DELETE | `/api/admin/users/{userId}` | `Admin\UserController@destroy` | 软删除（标记 `deleted`）| - |
| GET | `/api/admin/users/export` | `Admin\UserController@export` | 导出 CSV | 路由 `throttle:1,2` + 控制器内分钟计数与 30s 冷却 |

示例：更新状态请求体
```json
{ "status": "suspended" }
```
成功返回：`{ "success": true, "message": "User status updated successfully", "user": {...} }`

审计：`change_user_status`、`delete_user` 等会写入 `admin_activity_logs`。

#### 12.4 火车/车站/行程序列（Train/Station/Journey）
- 页面：`GET /train-management`（`Admin\TrainManagementController@index`）拉取 `Trains/Stations/Journeys` 列表。
- Web 写接口（表单/AJAX，走 Facade → Service）：

| 方法 | 路径 | 控制器方法 | 说明 |
| - | - | - | - |
| POST | `admin/train-management/train` | `storeTrain` | 新增火车 → `AdminFacade::addTrain` → `AdminModuleService::createTrain` |
| POST | `admin/train-management/station` | `storeStation` | 新增车站 → `AdminModuleService::createStation` |
| POST | `admin/train-management/journey` | `storeJourney` | 新增行程 → `AdminModuleService::createJourney` |
| POST | `admin/train-management/train/update` | `updateTrain` | 更新火车 → `AdminModuleService::updateTrain` |
| POST | `admin/train-management/station/update` | `updateStation` | 更新车站 |
| POST | `admin/train-management/journey/update` | `updateJourney` | 更新行程 |

`AdminModuleService` 校验重点：
- Train：`train_id`（更新时必填且存在）、`train_no`、`train_service`、`is_available in [Active,Unavailable]`、`station_id`（存在于 `Stations.StationID`）。
- Station：`station_id`（更新时必填且存在）、`station_name`、`location`、`is_active:boolean`。
- Journey：`journey_id`（更新时必填且存在）、`train_id`（存在）、`from_location`、`to_location!=from_location`、时间先后、`price>=0`、`status in [Scheduled,Delayed,Canceled]`（更新）。

日志：`add_train/update_train/add_station/update_station/add_journey/update_journey`。

Admin API 版（用于 Admin 页面 AJAX）

| 方法 | 路径 | 控制器方法 | 说明 |
| - | - | - | - |
| GET | `/api/admin/trains` | `AdminController@getTrains` | 代理获取列表 |
| POST | `/api/admin/trains` | `AdminController@addTrain` | 校验 name/route/capacity/departure/arrival（注：此分支走 API 预设字段，演示用途）|
| PUT | `/api/admin/trains/{id}` | `AdminController@updateTrain` | 同上 |
| DELETE | `/api/admin/trains/{id}` | `AdminController@deleteTrain` | 删除（如业务禁止可返回禁用提示）|

注意：权威数据写入建议统一走 `AdminModuleService`（Eloquent + 强校验 + 审计）。`TrainService` 为演示/占位实现。

#### 12.5 QR/票务（扫码、登检）
- 页面：`GET /admin/qr-scanner`
- 接口：

| 方法 | 路径 | 控制器方法 | 说明 |
| - | - | - | - |
| POST | `/api/admin/qr/scan` | `AdminController@scanQR` → `QRScannerService::scanQR` | 根据 QR 内容执行 `check-in/checkout` |
| POST | `/api/admin/qr/generate` | `AdminController@generateQR` → `QRScannerService::generateQR` | 生成含用户信息的 QR Base64 字串 |
| GET | `/api/admin/tickets/{ticketId}` | `Admin\TicketController@show` | 查看票 + 行程/列车/乘客信息 |
| POST | `/api/admin/tickets/{ticketId}/checkin` | `Admin\TicketController@checkIn` | 状态流转 `pending → checkin` |
| POST | `/api/admin/tickets/{ticketId}/checkout` | `Admin\TicketController@checkOut` | 状态流转 `checkin → checkout` |

票务状态严控：仅允许 `pending → checkin → checkout`，`paid` 视为 `pending` 兼容。越权流转会返回 422。

QR 数据格式（示例，Base64( JSON )）：
```json
{
  "user_id": 123,
  "user_email": "xxx@example.com",
  "user_name": "First Last",
  "type": "boarding|check-in|check-out",
  "timestamp": 1710000000,
  "token": "<32-chars>"
}
```

#### 12.6 Newsletter（简讯群发）
- 页面：`GET /admin/newsletter`
- 接口：

| 方法 | 路径 | 控制器方法 | 说明 |
| - | - | - | - |
| GET | `/api/admin/newsletter/subscribers` | `NewsletterController@list` | 列出订阅者 |
| POST | `/api/admin/newsletter/unsubscribe` | `NewsletterController@unsubscribe` | 退订 |
| POST | `/api/admin/newsletter/send` | `AdminController@sendNewsletter` → `NewsletterService::sendNewsletter` | 群发（收件人策略见下）|

收件人策略：`all`/`active`/`newsletter_subscribers`（合并内部勾选 newsletter + 外部 `NewsletterSubscriber` 表）、`verified`。

#### 12.7 退款（Refund）
- 页面：`GET /admin/refunds`
- 接口：`POST /api/admin/refunds/process` → `AdminController@processRefund` → `RefundService::processRefund`（演示/占位）。
- 统计：`RefundService::getRefundStats()` 返回各状态计数与金额占位值。

#### 12.8 系统信息（System Info）
- 页面：`GET /admin/system-info`
- 接口：`GET /api/admin/system/info` → `AdminService::getSystemInfo()`，返回 PHP/Laravel 版本、DB/Cache/Queue/Mail 驱动、环境、内存、时区、语言等。

#### 12.9 审计日志（Admin Activity Log）
- 表：`admin_activity_logs`
- 模型：`AdminActivityLog`（`details` JSON 自动转换）
- 记录器：`AdminActivityLogger::log(action, details)` 会注入：`ip`、`user_agent`、`actor_user_id`。
- 常见动作：`change_user_status`、`delete_user`、`add_train`、`update_train`、`add_station`、`update_station`、`add_journey`、`update_journey`、`ticket_checkin`、`ticket_checkout` 等。
- 查询：`GET /api/admin/logs`（注意：路由组中存在同一路径由不同控制器提供的情况，优先使用 `Admin\LogController::list` 版本）。

返回示例：
```json
{
  "success": true,
  "data": [
    { "admin_email": "admin@example.com", "action": "change_user_status", "details": {"target_user_id": 5, "new_status": "admin", "ip": "..." }, "created_at": "..." }
  ]
}
```

#### 12.10 错误处理与通用返回
- 成功：`{ "success": true, ... }` 或 `{ "status": "success", ... }`
- 失败：
  - 400/422 校验失败：`{ "success": false, "message": "..." }` 或附 `errors` 字段
  - 401 未通过 2FA/再认证：`{ "success": false, "message": "Re-authentication required ..." }`
  - 403 非管理员：`Forbidden`
  - 404 资源不存在
- 限流：对导出/敏感接口使用 `throttle`，配合 `admin.recent` 再认证中间件。

#### 12.11 典型操作流程（时序简述）
1) 管理员登录 → 命中 `auth` → 访问 `/admin/*` 命中 `admin`/`admin.2fa`
2) 若未完成 2FA：跳转 `admin.2fa.setup` 或 `admin.2fa.challenge` 完成验证
3) 进入 Dashboard，页面通过 AJAX 拉取统计/图表数据
4) 进行用户/火车/行程/票务/Newsletter/退款等操作（必要时触发 `admin.recent` 再认证）
5) 每次关键操作写入审计日志，供日志页面/API 查询

#### 12.12 配置要点
- `config('app.api_base_url')`：`AdminController` 调用下游 API 的基址（默认 `http://localhost:8001/api`）。
- 数据库表名大小写以迁移为准：例 `Trains/Stations/Journeys`（代码中按既有模型字段）。
- 生产建议：将占位 Service（如 `TrainService`、`RefundService`）替换为真实实现，统一走 `AdminModuleService` 做强校验与审计。

