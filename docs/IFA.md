## Interface Agreement (IFA) – Web Services Report

Scope: This report documents web service exposure and consumption for three modules in this codebase: User, Admin, and Concession Card. It lists concrete REST endpoints, real request/response formats inferred from controllers, and actual consuming modules/components found in the code.

### 1) User Module – Service Exposure and Consumption

Source Module: User

Webservice Mechanism

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | Authenticate user by email/password |
| Source Module | User |
| Target Module | Frontend (Login form), any client needing login |
| URL | `/api/user/login` |
| Function Name | `login` |
| Request | `{ email: string, password: string }` |
| Success Response | `{ status: 'success', message: string, data: { userId: number, userName: string, userEmail: string, accessToken: string, tokenType: 'Bearer', expiresIn: number } }` |
| Error Response | `{ status: 'error', message: 'Invalid credentials', data: null }` |

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | Register new user |
| Source Module | User |
| Target Module | Frontend (Signup form) |
| URL | `/api/user/register` |
| Function Name | `register` |
| Request | `{ name: string, email: string, password: string, password_confirmation: string, phone?: string, address?: string }` |
| Success Response | `{ status: 'success', message: string, data: { userId, userName, userEmail, accessToken, tokenType, expiresIn } }` |

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | Issue password reset token |
| Source Module | User |
| Target Module | Frontend (Forgot-password flow) |
| URL | `/api/user/forgot-password` |
| Function Name | `forgotPassword` |
| Request | `{ email: string }` |
| Success Response | `{ status: 'success', message: string, data: { resetToken: string, expiresIn: number, instructions: string } }` |

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | Reset password with token |
| Source Module | User |
| Target Module | Frontend (Reset form) |
| URL | `/api/user/reset-password` |
| Function Name | `resetPassword` |
| Request | `{ email: string, token: string, password: string, password_confirmation: string }` |
| Success Response | `{ status: 'success', message: string, data: { userId, userName, userEmail } }` |

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | Get user profile by ID |
| Source Module | User |
| Target Module | Frontend or other modules needing profile |
| URL | `/api/user/profile/{userId}` |
| Function Name | `getProfile` |
| Request | Path: `userId` (alnum) |
| Success Response | `{ status: 'success', message: string, data: { status: 'A'|'I', userName: string, userEmail: string, userDetails: { HpNo: string, HouseAdd: string, createdAt: string, lastLogin: string } } }` |

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | Update user profile |
| Source Module | User |
| Target Module | Frontend user settings |
| URL | `/api/user/profile/{userId}` (PUT) |
| Function Name | `updateProfile` |
| Request | `{ name?: string, phone?: string, address?: string }` |
| Success Response | `{ status: 'success', message: string, data: { userId, userName, userEmail, userDetails: { HpNo, HouseAdd } } }` |

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | List users (search/status/pagination) |
| Source Module | User |
| Target Module | Admin Module |
| URL | `/api/user/list` |
| Function Name | `listUsers` |
| Request | Query: `search?: string, status?: string, per_page?: number` |
| Success Response | `{ success: true, data: Pagination<User> }` (Laravel paginator JSON) |

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | Update user status |
| Source Module | User |
| Target Module | Admin Module |
| URL | `/api/user/{userId}/status` (PUT) |
| Function Name | `updateUserStatus` |
| Request | `{ status: 'active'|'suspended'|'not_verified'|'admin' }` |
| Success Response | `{ success: true, message: string, user: User }` |

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | Soft delete user |
| Source Module | User |
| Target Module | Admin Module |
| URL | `/api/user/{userId}` (DELETE) |
| Function Name | `deleteUser` |
| Success Response | `{ success: true, message: string }` |

Consumption (who calls User APIs):

- Admin Module: `AdminController` proxies to User APIs for users list/status/delete (see calls to `${apiBaseUrl}/user/list`, `/user/{id}/status`, `/user/{id}`).

#### Request/Response Parameters (Provide/Consume)

Only documenting web services consumed by other modules (Admin).

1) List Users – `/api/user/list` (GET)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| search | String | Optional | Keyword to match first_name/last_name/email | Free text, trimmed |
| status | String | Optional | Filter by account status | One of: `active`, `suspended`, `not_verified`, `admin`, `deleted` |
| per_page | Integer | Optional | Page size | Positive integer, default 10 |
| page | Integer | Optional | Page number | Positive integer (Laravel paginator) |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| success | Boolean | Mandatory | Operation status | true/false |
| data | Object | Mandatory | Laravel pagination payload | `{ current_page, data: User[], per_page, total, last_page, ... }` |

User object (subset): `{ user_id:number, first_name:string, last_name:string, email:string, account_status:string, created_at:datetime, last_login_at:datetime|null }`

2) Update User Status – `/api/user/{userId}/status` (PUT)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| userId | String (path) | Mandatory | Unique numeric ID of the user | Digits only (`^[0-9]+$`) |
| status | String | Mandatory | New account status | One of: `active`, `suspended`, `not_verified`, `admin` |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| success | Boolean | Mandatory | Status of the request | true/false |
| message | String | Mandatory | Result message | Human readable |
| user | Object | Mandatory | Updated user | Same shape as User (subset above) |

3) Delete User – `/api/user/{userId}` (DELETE)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| userId | String (path) | Mandatory | Unique numeric ID of the user | Digits only (`^[0-9]+$`) |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| success | Boolean | Mandatory | Status of the request | true/false |
| message | String | Mandatory | Result message | Human readable |


### 2) Admin Module – Service Exposure and Consumption

Source Module: Admin

Representative Webservice Mechanism (subset)

| Protocol | Function Description | URL | Function Name | Target Module |
|---|---|---|---|---|
| REST/JSON | Dashboard datasets (stats/filters/trips/users/profit) | `/api/admin/dashboard/(stats|filters|trips|users-growth|profit)` | `getDashboardStats`, `getDashboardFilters`, `getTripsPerMonth`, `getUsersGrowth`, `getProfitTrends` | Admin Frontend pages |
| REST/JSON | Trains CRUD | `/api/admin/trains` (GET/POST), `/api/admin/trains/{id}` (PUT/DELETE) | `getTrains`, `addTrain`, `updateTrain`, `deleteTrain` | Admin Frontend pages |
| REST/JSON | Newsletter ops | `/api/admin/newsletter/(subscribers|unsubscribe|send)` | `list`, `unsubscribe`, `sendNewsletter` | Admin Frontend pages |
| REST/JSON | Export (issue/download) | `/api/admin/export`, `/api/admin/export/download` | `exportData`, `downloadExport` | Admin Frontend pages |
| REST/JSON | Logs list | `/api/admin/logs` | `list` | Admin Frontend pages |
| REST/JSON | Ticket lookup/check-in/out | `/api/admin/tickets/{ticketId}`, `/checkin`, `/checkout` | `show`, `checkIn`, `checkOut` | Admin ScanQR page |
| REST/JSON | Provide Journey by ID (Admin → Booking) | `/api/admin/journey/{journeyId}` (GET) | `getJourneyById` | Booking Module |

Consumption (who Admin calls):

- User Module: Admin users management proxies to `/api/user/list`, `/api/user/{id}/status`, `/api/user/{id}`.
- Booking/Ticket (shared via API route): Admin ScanQR frontend now calls generic `/api/tickets/*` endpoints (registered in `routes/api.php`) which use `Admin\\TicketController` to operate on tickets.

#### Request/Response Parameters (Provide/Consume)

Only documenting cross-module endpoints shared for consumption (Ticket APIs under common `/api/tickets` used by Admin ScanQR, but available to other modules).

1) Get Ticket Info – `/api/tickets/{ticketId}` (GET)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| ticketId | String (path) | Mandatory | Unique Ticket ID | Non-empty string |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| success | Boolean | Mandatory | Status | true/false |
| data | Object | Mandatory | Ticket composite info | `{ ticketId, status, passenger:{id,name}, journey:{id,from,to,departure,arrival}, train:{id,no,service} }` |

2) Check In Ticket – `/api/tickets/{ticketId}/checkin` (POST)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| ticketId | String (path) | Mandatory | Unique Ticket ID | Non-empty string |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| success | Boolean | Mandatory | Status | true/false |
| message | String | Mandatory | Result message | Human readable |
| data | Object | Mandatory | Updated status payload | `{ ticketId, status }` |

3) Check Out Ticket – `/api/tickets/{ticketId}/checkout` (POST)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| ticketId | String (path) | Mandatory | Unique Ticket ID | Non-empty string |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| success | Boolean | Mandatory | Status | true/false |
| message | String | Mandatory | Result message | Human readable |
| data | Object | Mandatory | Updated status payload | `{ ticketId, status }` |

Admin Web Service (for other modules)

A) Provide Journey Details for Booking – `/api/admin/journey/{journeyId}` (GET)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| journeyId | String (path) | Mandatory | Unique ID of the journey | Alphanumeric (`^[a-zA-Z0-9]+$`) |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| TrainID | String | Mandatory | TrainID of the journey | Alphanumeric |
| FromLocation | String | Mandatory | Depart location | Alphanumeric/space |
| ToLocation | String | Mandatory | Arrive location | Alphanumeric/space |
| DepartureTime | Datetime | Mandatory | Departure time of journey | `YYYY-MM-DD HH:MM:SS` |
| ArrivalTime | Datetime | Mandatory | Arrival time of journey | `YYYY-MM-DD HH:MM:SS` |
| SeatAvailable | Integer | Mandatory | Number of seat availability | Digits only |
| Price | Decimal | Mandatory | Price of journey | Number with 2 decimal points |

B) Get User Info – `/api/admin/user/{userId}` (GET)
B) 其余 Admin Web Services

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| userId | String (path) | Mandatory | Unique ID of the user | Can only contain alphabet and number |
| queryFlag | Integer (query) | Mandatory | Flag on information needed | 1: get Contact No. 2: get Address 3: both |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| status | String | Mandatory | Status of the request payload | A: Active, I: Inactive |
| userName | String | Mandatory | Name of the user | Can only contain alphabet and number |
| userEmail | String | Optional | Email of the user | Only acceptable email address |
| userDetails | Object | Mandatory | Details returned based on queryFlag | `HpNo`: Handphone No.; `HouseAdd`: House Address |

B) Get Concession Application – `/api/admin/concession/{applicationId}` (GET)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| applicationId | String (path) | Mandatory | Unique ID of concession application | Alphanumeric |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| status | String | Mandatory | Status of the request | `success` or `error` |
| applicationId | String | Mandatory | Application identifier | Alphanumeric |
| type | String | Mandatory | Application type | Free text |
| fullName | String | Mandatory | Applicant name | Alphabet and space |
| statusText | String | Mandatory | Current application status | Free text (e.g., Pending/Approved) |
| appliedDate | String (ISO) | Mandatory | Created timestamp | ISO-8601 |
| userDetails | Object | Mandatory | Linked user summary | `{ userId:number, userName:string, userEmail:string }` |

C) Publish Concession Decision – `/api/admin/concession/decision` (POST)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| applicationId | String | Mandatory | Unique ID of the application | Alphanumeric (e.g., APP001) |
| decision | String | Mandatory | Decision value | `approve` or `reject` |
| remark | String | Optional | Admin remarks | Max length 500 |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| status | String | Mandatory | Status of the request | `success` or `error` |
| message | String | Mandatory | Result text | Human readable |
| data | Object | Mandatory | Decision result | `{ applicationId: string, status: 'approved'|'rejected', reviewedAt: ISOString }` |


### 3) Concession Card Module – Service Exposure and Consumption

Source Module: Concession Card

Webservice Mechanism

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | List user applications |
| Source Module | Concession |
| Target Module | Frontend Concession flows |
| URL | `/api/concession/user/{userId}` |
| Function Name | `getUserApplications` |

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | Get application details |
| Source Module | Concession |
| Target Module | Frontend/Admin viewers |
| URL | `/api/concession/application/{applicationId}` |
| Function Name | `getApplicationDetails` |

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | Submit application |
| Source Module | Concession |
| Target Module | Frontend Concession flows |
| URL | `/api/concession/application` (POST) |
| Function Name | `submitApplication` |

| Description | Value |
|---|---|
| Protocol | REST/JSON |
| Function Description | Concession statistics |
| Source Module | Concession |
| Target Module | Dashboards/Reports |
| URL | `/api/concession/statistics` |
| Function Name | `getStatistics` |

Consumption (who calls Concession APIs):

- Frontend concession pages (web routes under `ConcessionCardController`) handle user flows; APIs are available for integration. Admin has an admin-facing endpoint `/api/admin/concession/{applicationId}` in `AdminWebServiceController` that exposes application details for admin use.

At present, Concession APIs are primarily consumed by its own frontend flow. As per instruction, request/response parameter tables are omitted for own-frontend-only services.


Notes on Security Alignment

- User Module uses `Hash::make`/`Hash::check` for password hashing and validates inputs. Login returns unified error messages to avoid user enumeration.
- Admin Module endpoints are protected by `admin`, `admin.2fa`, and for sensitive operations, `admin.recent` plus `throttle`.
- Ticket QR scan now uses shared `/api/tickets/*` endpoints so other modules could integrate consistently.


### 4) Booking Module – Service Exposure and Consumption

Source Module: Booking

Webservice Mechanism (primary endpoints)

| Protocol | Function Description | URL | Function Name | Target Module |
|---|---|---|---|---|
| REST/JSON | List bookings by user | `/api/bookings/{userId}` (GET) | `index` | Frontend User Booking pages |
| REST/JSON | Get booking details | `/api/booking/{bookingId}/{userId}` (GET) | `show` | Frontend Booking Detail/Payment |
| REST/JSON | Cancel a booking | `/api/booking/cancel/{bookingId}/{userId}` (PATCH) | `cancel` | Frontend Booking Management |
| REST/JSON | Ticket info/check-in/checkout | `/api/tickets/{ticketId}` (GET), `/api/tickets/{ticketId}/checkin` (POST), `/api/tickets/{ticketId}/checkout` (POST) | `show`, `checkIn`, `checkOut` | Admin ScanQR (cross-module) |

Cross-module consumption: The Ticket endpoints are consumed by the Admin module ScanQR page. The other booking endpoints are primarily consumed by the Booking module’s own frontend.

1) List Bookings – `/api/bookings/{userId}` (GET)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| userId | String (path) | Mandatory | Unique numeric ID of the user | Digits only (`^[0-9]+$`) |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| ongoing | Array | Mandatory | Ongoing bookings for the user | Array of Booking summary (with `journey`, `journey.train`) |
| past | Array | Mandatory | Completed bookings | Array of Booking summary including `hasFeedback: bool`, `showRateTrip: bool` |
| refunded | Array | Mandatory | Refunded/Cancelled bookings | Array of Booking summary |

2) Get Booking Details – `/api/booking/{bookingId}/{userId}` (GET)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| bookingId | String (path) | Mandatory | Unique booking ID | Digits only |
| userId | String (path) | Mandatory | Owner user ID | Digits only |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| booking | Object | Mandatory | Booking with relationships | Includes `journey`, `journey2`, `journey.train` |
| tickets | Array | Mandatory | Tickets in the booking | Array of Ticket with `seat`, `passenger`, `journey` |

3) Cancel Booking – `/api/booking/cancel/{bookingId}/{userId}` (PATCH)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| bookingId | String (path) | Mandatory | Booking ID to cancel | Digits only |
| userId | String (path) | Mandatory | Owner user ID | Digits only |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| message | String | Mandatory | Result message | `Booking cancelled successfully` on success, or error text |

4) Get Ticket Info – `/api/tickets/{ticketId}` (GET)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| ticketId | String (path) | Mandatory | Unique Ticket ID | Non-empty string |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| success | Boolean | Mandatory | Status | true/false |
| data | Object | Mandatory | Ticket composite info | `{ ticketId, status, passenger:{id,name}, journey:{id,from,to,departure,arrival}, train:{id,no,service} }` |

5) Check In Ticket – `/api/tickets/{ticketId}/checkin` (POST)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| ticketId | String (path) | Mandatory | Unique Ticket ID | Non-empty string |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| success | Boolean | Mandatory | Status | true/false |
| message | String | Mandatory | Result message | Human readable |
| data | Object | Mandatory | Updated status payload | `{ ticketId, status }` |

6) Check Out Ticket – `/api/tickets/{ticketId}/checkout` (POST)

Web Services Request Parameter (provide)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| ticketId | String (path) | Mandatory | Unique Ticket ID | Non-empty string |

Web Services Response Parameter (consume)

| Field Name | Field Type | Mandatory/Optional | Description | Format |
|---|---|---|---|---|
| success | Boolean | Mandatory | Status | true/false |
| message | String | Mandatory | Result message | Human readable |
| data | Object | Mandatory | Updated status payload | `{ ticketId, status }` |


