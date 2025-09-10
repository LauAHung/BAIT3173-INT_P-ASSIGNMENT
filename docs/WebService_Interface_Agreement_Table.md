# Web Service Interface Agreement (IFA) - Complete Table

## Web Service Technology Overview
- **Protocol**: REST API (JSON-based)
- **Authentication**: Token-based using Laravel Sanctum
- **Data Format**: JSON
- **Base URL**: `http://localhost:8000/api`
- **CORS**: Enabled for cross-origin requests

---

## 1. User Module Web Services

### 1.1 User Login Service

| Field | Description / Example |
|-------|----------------------|
| **Protocol** | REST |
| **Function** | Authenticates user and returns access token |
| **Description** | User Management Module |
| **Source Module** | User Module |
| **Target Module** | Authentication System, Session Management, Concession Card Module |
| **URL** | `http://localhost:8000/api/user/login` |
| **Function Name** | `login` |

#### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `email` | String | Mandatory | User email address | Valid email format |
| `password` | String | Mandatory | User password | Minimum 6 characters |

#### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `status` | String | Mandatory | Request status | "success" or "error" |
| `message` | String | Mandatory | Response message | Descriptive text |
| `data` | Object | Mandatory | User data and token | Contains userId, userName, userEmail, accessToken |

### 1.2 User Registration Service

| Field | Description / Example |
|-------|----------------------|
| **Protocol** | REST |
| **Function** | Registers a new user account |
| **Description** | User Management Module |
| **Source Module** | User Module |
| **Target Module** | User Management, Account System, Admin Module |
| **URL** | `http://localhost:8000/api/user/register` |
| **Function Name** | `register` |

#### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `name` | String | Mandatory | User full name | Max 255 characters |
| `email` | String | Mandatory | User email address | Valid email format, unique |
| `password` | String | Mandatory | User password | Minimum 6 characters, confirmed |
| `phone` | String | Optional | User phone number | Max 20 characters |
| `address` | String | Optional | User address | Max 500 characters |

#### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `status` | String | Mandatory | Request status | "success" or "error" |
| `message` | String | Mandatory | Response message | Descriptive text |
| `data` | Object | Mandatory | User data and token | Contains userId, userName, userEmail, accessToken |

### 1.3 Get User Profile Service

| Field | Description / Example |
|-------|----------------------|
| **Protocol** | REST |
| **Function** | Retrieves user profile information |
| **Description** | User Management Module |
| **Source Module** | User Module |
| **Target Module** | Concession Card Module, Admin Module, Profile Management |
| **URL** | `http://localhost:8000/api/user/profile/{userId}` |
| **Function Name** | `getProfile` |

#### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `userId` | String | Mandatory | Unique user identifier | Alphanumeric only |

#### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `status` | String | Mandatory | User status | "A" (Active) or "I" (Inactive) |
| `userName` | String | Mandatory | User full name | Alphanumeric characters |
| `userEmail` | String | Mandatory | User email address | Valid email format |
| `userDetails` | Object | Mandatory | Detailed user information | Contains HpNo, HouseAdd, createdAt, lastLogin |

---

## 2. Admin Module Web Services

### 2.1 Get User Information Service

| Field | Description / Example |
|-------|----------------------|
| **Protocol** | REST |
| **Function** | Retrieves user information by user ID |
| **Description** | Admin Management Module |
| **Source Module** | Admin Module |
| **Target Module** | Customer Service, Analytics Module, Concession Card Module |
| **URL** | `http://localhost:8000/api/admin/user/{userId}` |
| **Function Name** | `getUserInfo` |

#### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `userId` | String | Mandatory | Unique user identifier | Alphanumeric only |
| `queryFlag` | Integer | Optional | Information needed flag | 1: Contact No., 2: Address, 3: Both |

#### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `status` | String | Mandatory | User status | "A" (Active) or "I" (Inactive) |
| `userName` | String | Mandatory | User full name | Alphanumeric characters |
| `userEmail` | String | Mandatory | User email address | Valid email format |
| `userDetails` | Object | Mandatory | Detailed user information | Contains HpNo, HouseAdd based on queryFlag |

### 2.2 Get Concession Application Service

| Field | Description / Example |
|-------|----------------------|
| **Protocol** | REST |
| **Function** | Retrieves concession application details |
| **Description** | Admin Management Module |
| **Source Module** | Admin Module |
| **Target Module** | Concession Management, Reporting, Analytics |
| **URL** | `http://localhost:8000/api/admin/concession/{applicationId}` |
| **Function Name** | `getConcessionApplication` |

#### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `applicationId` | String | Mandatory | Unique application identifier | Alphanumeric only |

#### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `applicationId` | String | Mandatory | Application identifier | Alphanumeric |
| `type` | String | Mandatory | Application type | "oku", "senior", or "student" |
| `fullName` | String | Mandatory | Applicant full name | Text |
| `status` | String | Mandatory | Application status | "pending", "approved", or "rejected" |
| `appliedDate` | String | Mandatory | Application date | ISO 8601 format |
| `userDetails` | Object | Mandatory | User information | Contains userId, userName, userEmail |

---

## 3. Concession Card Module Web Services

### 3.1 Get User Applications Service

| Field | Description / Example |
|-------|----------------------|
| **Protocol** | REST |
| **Function** | Retrieves user's concession applications |
| **Description** | Concession Card Management Module |
| **Source Module** | Concession Card Module |
| **Target Module** | User Module, Admin Module, User Dashboard |
| **URL** | `http://localhost:8000/api/concession/user/{userId}` |
| **Function Name** | `getUserApplications` |

#### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `userId` | String | Mandatory | Unique user identifier | Alphanumeric only |

#### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `userId` | String | Mandatory | User identifier | Alphanumeric |
| `totalApplications` | Integer | Mandatory | Total number of applications | Numeric |
| `applications` | Array | Mandatory | List of applications | Array of application objects |

### 3.2 Submit Concession Application Service

| Field | Description / Example |
|-------|----------------------|
| **Protocol** | REST |
| **Function** | Submits new concession application |
| **Description** | Concession Card Management Module |
| **Source Module** | Concession Card Module |
| **Target Module** | User Module, Admin Module, Application Processing |
| **URL** | `http://localhost:8000/api/concession/application` |
| **Function Name** | `submitApplication` |

#### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `type` | String | Mandatory | Application type | "oku", "senior", or "student" |
| `fullName` | String | Mandatory | Applicant full name | Max 255 characters |
| `ic` | String | Mandatory | IC number | Max 12 characters |
| `passportNumber` | String | Optional | Passport number | Max 20 characters |
| `okuCardNumber` | String | Conditional | OKU card number | Required if type=oku |
| `disabilityType` | String | Conditional | Disability type | Required if type=oku |
| `matrixNumber` | String | Conditional | Matrix number | Required if type=student |
| `schoolName` | String | Conditional | School name | Required if type=student |

#### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `applicationId` | String | Mandatory | Application identifier | Alphanumeric |
| `type` | String | Mandatory | Application type | "oku", "senior", or "student" |
| `status` | String | Mandatory | Application status | "pending" |
| `appliedDate` | String | Mandatory | Application date | ISO 8601 format |

### 3.3 Get Concession Statistics Service

| Field | Description / Example |
|-------|----------------------|
| **Protocol** | REST |
| **Function** | Retrieves concession application statistics |
| **Description** | Concession Card Management Module |
| **Source Module** | Concession Card Module |
| **Target Module** | Admin Module, Analytics, Reporting |
| **URL** | `http://localhost:8000/api/concession/statistics` |
| **Function Name** | `getStatistics` |

#### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `queryFlag` | Integer | Optional | Information needed flag | 1: Basic stats, 2: With type breakdown, 3: With recent applications |

#### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `status` | String | Mandatory | Service status | "A" (Active) |
| `totalApplications` | Integer | Mandatory | Total applications | Numeric |
| `pendingApplications` | Integer | Mandatory | Pending applications | Numeric |
| `approvedApplications` | Integer | Mandatory | Approved applications | Numeric |
| `rejectedApplications` | Integer | Mandatory | Rejected applications | Numeric |
| `typeBreakdown` | Object | Optional | Applications by type | Contains oku, senior, student counts |
| `recentApplications` | Array | Optional | Recent applications | Array of recent application objects |

---

## 4. Booking Module Web Services

### 4.1 Get Bookings Service

| Field | Description / Example |
|-------|----------------------|
| **Protocol** | REST |
| **Function** | Retrieves user bookings |
| **Description** | Booking Management Module |
| **Source Module** | Booking Module |
| **Target Module** | User Module, Admin Module, Booking Management |
| **URL** | `http://localhost:8000/api/bookings/bookings` |
| **Function Name** | `index` |

#### Request Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| None | - | - | No specific parameters | - |

#### Response Parameters
| Field Name | Field Type | Mandatory/Optional | Description | Format |
|------------|------------|-------------------|-------------|---------|
| `bookings` | Array | Mandatory | List of bookings | Array of booking objects |
| `totalBookings` | Integer | Mandatory | Total number of bookings | Numeric |

---

## 5. Cross-Module Communication Examples

### 5.1 User Module → Concession Card Module
- **Purpose**: Get user profile when processing concession application
- **Flow**: User submits application → Concession Card Module calls User Module API to get user details
- **API Call**: `GET /api/user/profile/{userId}`

### 5.2 Concession Card Module → Admin Module
- **Purpose**: Get concession application details for admin review
- **Flow**: Admin reviews application → Admin Module calls Concession Card Module API
- **API Call**: `GET /api/concession/application/{applicationId}`

### 5.3 Admin Module → User Module
- **Purpose**: Get user information for admin dashboard
- **Flow**: Admin views dashboard → Admin Module calls User Module API
- **API Call**: `GET /api/admin/user/{userId}`

### 5.4 Multi-Module Data Aggregation
- **Purpose**: Get comprehensive data from multiple modules
- **Flow**: Admin dashboard → Calls multiple APIs simultaneously
- **API Calls**: 
  - `GET /api/concession/statistics`
  - `GET /api/admin/logs`
  - `GET /api/admin/newsletter/subscribers`

---

## 6. Error Handling

All web services follow a consistent error response format:

```json
{
    "status": "error",
    "message": "Error description",
    "errors": {
        "field_name": ["Validation error message"]
    },
    "data": null
}
```

## 7. Authentication

- **Method**: Bearer Token Authentication
- **Header**: `Authorization: Bearer {token}`
- **Token Source**: User login API response
- **Token Expiry**: 3600 seconds (1 hour)

## 8. Rate Limiting

- **Default**: 60 requests per minute per IP
- **Authenticated**: 1000 requests per minute per user
- **Headers**: `X-RateLimit-Limit`, `X-RateLimit-Remaining`
