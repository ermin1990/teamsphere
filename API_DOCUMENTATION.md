# TeamSphere API Documentation

## Overview

TeamSphere API provides RESTful access to sports league management functionality. The API is versioned under `/api/v1/` prefix and supports both public and authenticated endpoints.

## Authentication

Most API endpoints require authentication using Laravel Sanctum. Include the Bearer token in the Authorization header:

```
Authorization: Bearer {token}
```

## Response Format

All API responses follow a consistent format:

```json
{
  "success": true|false,
  "data": { ... } | [...],
  "message": "Description of the response"
}
```

## Public Endpoints

### Sports

#### GET /api/v1/sports
Get all available sports.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Stoni Tenis",
      "slug": "stoni-tenis",
      "description": "Brzi i dinamični sport...",
      "icon": "🏓",
      "active": true,
      "leagues_count": 2
    }
  ],
  "message": "Sports retrieved successfully"
}
```

#### GET /api/v1/sports/{sport}
Get a specific sport with its public leagues.

### Leagues

#### GET /api/v1/leagues
Get all public leagues.

#### GET /api/v1/leagues/{league}
Get a specific public league with matches and players.

### Matches

#### GET /api/v1/matches
Get all public matches.

#### GET /api/v1/matches/{match}
Get a specific public match.

### Organizations

#### GET /api/v1/organizations
Get all public organizations.

#### GET /api/v1/organizations/{organization}
Get a specific public organization.

### Players

#### GET /api/v1/players
Get all public players.

#### GET /api/v1/players/{player}
Get a specific public player.

## Authenticated Endpoints

### Profile Management

#### GET /api/v1/profile
Get authenticated user's profile.

#### PUT /api/v1/profile
Update user profile.

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com"
}
```

#### PUT /api/v1/profile/password
Update user password.

**Request:**
```json
{
  "current_password": "oldpassword",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

### Organizations

#### GET /api/v1/my-organizations
Get user's organizations.

#### POST /api/v1/organizations
Create a new organization.

#### PUT /api/v1/organizations/{organization}
Update organization.

#### DELETE /api/v1/organizations/{organization}
Delete organization.

### Leagues

#### GET /api/v1/organizations/{organization}/leagues
Get organization leagues.

#### POST /api/v1/organizations/{organization}/leagues
Create league in organization.

#### PUT /api/v1/leagues/{league}
Update league.

#### DELETE /api/v1/leagues/{league}
Delete league.

#### GET /api/v1/leagues/{league}/standings
Get league standings.

### Matches

#### GET /api/v1/leagues/{league}/matches
Get league matches.

#### POST /api/v1/leagues/{league}/matches
Create match in league.

#### PUT /api/v1/matches/{match}
Update match.

#### DELETE /api/v1/matches/{match}
Delete match.

#### POST /api/v1/matches/{match}/score
Update match score.

#### POST /api/v1/matches/{match}/status
Update match status.

### Players

#### GET /api/v1/leagues/{league}/players
Get league players.

#### POST /api/v1/leagues/{league}/players
Add player to league.

#### PUT /api/v1/players/{player}
Update player.

#### DELETE /api/v1/players/{player}
Remove player.

### Tables

#### GET /api/v1/leagues/{league}/tables
Get league tables.

#### POST /api/v1/leagues/{league}/tables
Create table in league.

#### PUT /api/v1/tables/{table}
Update table.

#### DELETE /api/v1/tables/{table}
Delete table.

## Error Handling

API returns appropriate HTTP status codes:

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

Error responses include validation messages when applicable.