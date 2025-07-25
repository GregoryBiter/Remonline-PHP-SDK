# Changelog

All notable changes to this project will be documented in this file.

## [2.0.0] - 2025-07-25

### BREAKING CHANGES
- **Authentication**: Switched from temporary token system to Bearer Token authentication
- **API Requirements**: Now requires API key from RemOnline Settings > API section
- **Rate Limits**: Added support for 3 requests per second rate limiting

### Added
- Bearer Token authentication using `Authorization: Bearer YOUR_API_KEY` header
- Rate limit handling (HTTP 429 responses)
- Improved error handling for unauthorized requests (HTTP 401)
- Better HTTP status code validation
- Enhanced logging with proper error categorization
- Added `getApiClient()` method to RemonlineClient for low-level access
- Added `getApiKey()` method to Api class
- Comprehensive documentation updates

### Changed
- **Api class**: Complete rewrite to use Bearer Token authentication
- **RemonlineClient class**: Now uses Api class internally instead of duplicate code
- **Error handling**: More specific error messages for different failure scenarios
- **Method signatures**: Improved type hints and parameter validation
- **Logging**: Fixed typo in logger name ('debag' → 'debug')

### Removed
- `getToken()` method - no longer needed with Bearer authentication
- `checkToken()` method - tokens don't expire anymore
- `toUrl()` method - replaced with standard `http_build_query()`
- Temporary token management logic
- DateTime dependency (no longer needed)

### Fixed
- Improved pagination logic in `getData()` method
- Better handling of different API response formats
- Proper cURL resource management

### Technical Details
- Base URL remains: `https://api.remonline.app/`
- Maximum 3 requests per second
- Up to 50 entries per pagination request
- JSON response format maintained

### Migration Guide

#### Before (v1.x):
```php
$api = new RemonlineClient("api_key");
// Token was automatically managed
```

#### After (v2.0):
```php
// Get your API key from RemOnline Settings > API
$api = new RemonlineClient("your_api_key_from_settings"); 
// Direct Bearer token authentication
```

The API usage remains the same:
```php
$order = new Order($api);
$orders = $order->getOrders();
```
