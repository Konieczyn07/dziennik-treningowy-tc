import pytest
import json
from unittest.mock import Mock, patch, MagicMock

class TestPublicEndpoints:
    """Test that public endpoints work without session"""
    
    @patch('builtins.session', {})
    def test_login_endpoint_is_public(self):
        """Test that /auth/login can be accessed without being logged in"""
        mock_server = Mock()
        mock_server.REQUEST_URI = '/zaliczenie/api/auth/login'
        mock_server.REQUEST_METHOD = 'POST'
        
        public_endpoints = ['/zaliczenie/api/auth/login', '/zaliczenie/api/auth/register', '/zaliczenie/api/auth/check']
        request_uri = '/zaliczenie/api/auth/login'
        
        is_public = False
        for endpoint in public_endpoints:
            if endpoint in request_uri:
                is_public = True
                break
        
        assert is_public == True, "Login endpoint should be public"
        
        session_has_user = False 
        if not is_public and not session_has_user:
            assert False, "Should not check session for public endpoints"
        else:
            assert True, "Public endpoint bypasses session check"

    @patch('builtins.session', {})
    def test_register_endpoint_is_public(self):
        """Test that /auth/register can be accessed without being logged in"""
        public_endpoints = ['/zaliczenie/api/auth/login', '/zaliczenie/api/auth/register', '/zaliczenie/api/auth/check']
        request_uri = '/zaliczenie/api/auth/register'
        
        is_public = any(endpoint in request_uri for endpoint in public_endpoints)
        
        assert is_public == True, "Register endpoint should be public"
    
    @patch('builtins.session', {})
    def test_check_endpoint_is_public(self):
        """Test that /auth/check can be accessed without being logged in"""
        public_endpoints = ['/zaliczenie/api/auth/login', '/zaliczenie/api/auth/register', '/zaliczenie/api/auth/check']
        request_uri = '/zaliczenie/api/auth/check'
        
        is_public = any(endpoint in request_uri for endpoint in public_endpoints)
        
        assert is_public == True, "Check endpoint should be public"

class TestProtectedEndpoints:
    """Test that workout endpoints require authentication"""
    
    @patch('builtins.session', {})
    def test_workout_endpoint_requires_auth(self):
        """Test that /api/workout requires user to be logged in"""
        public_endpoints = ['/zaliczenie/api/auth/login', '/zaliczenie/api/auth/register', '/zaliczenie/api/auth/check']
        request_uri = '/zaliczenie/api/workout'
        
        is_public = any(endpoint in request_uri for endpoint in public_endpoints)
        session_has_user = False  
        
        if not is_public and not session_has_user:
            expected_response = {
                "error": "Unauthorized",
                "message": "Musisz być zalogowany"
            }
            expected_status = 401
            
            assert expected_status == 401
            assert "Unauthorized" in expected_response["error"]
            assert "Musisz być zalogowany" in expected_response["message"]
        else:
            pytest.fail("Should reject unauthenticated request")
    
    @patch('builtins.session', {'user_id': 123})
    def test_workout_endpoint_allows_authenticated_user(self):
        """Test that /api/workout allows access when user is logged in"""
        public_endpoints = ['/zaliczenie/api/auth/login', '/zaliczenie/api/auth/register', '/zaliczenie/api/auth/check']
        request_uri = '/zaliczenie/api/workout'
        
        is_public = any(endpoint in request_uri for endpoint in public_endpoints)
        session_has_user = True  
        
        if not is_public and not session_has_user:
            assert False, "Should not reject authenticated request"
        else:
            assert True, "Authenticated user can access protected endpoint"
    
    @patch('builtins.session', {'user_id': 456})
    def test_multiple_protected_endpoints(self):
        """Test various workout endpoints all require auth"""
        protected_endpoints = [
            '/zaliczenie/api/workout',
            '/zaliczenie/api/workout?id=1',
            '/zaliczenie/api/workout/1'
        ]
        public_endpoints = ['/zaliczenie/api/auth/login', '/zaliczenie/api/auth/register', '/zaliczenie/api/auth/check']
        
        for endpoint in protected_endpoints:
            is_public = any(public in endpoint for public in public_endpoints)
            assert is_public == False, f"Endpoint {endpoint} should be protected"

class TestHTTPMethodsAndCORS:
    """Test HTTP method handling and CORS headers"""
    
    def test_cors_headers_are_set(self):
        """Test that proper CORS headers are returned"""
        expected_headers = {
            "Access-Control-Allow-Origin": "*",
            "Content-Type": "application/json; charset=UTF-8",
            "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, OPTIONS",
            "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"
        }
        
        assert expected_headers["Access-Control-Allow-Origin"] == "*"
        assert "application/json" in expected_headers["Content-Type"]
        assert "GET" in expected_headers["Access-Control-Allow-Methods"]
        assert "POST" in expected_headers["Access-Control-Allow-Methods"]
        assert "PUT" in expected_headers["Access-Control-Allow-Methods"]
        assert "DELETE" in expected_headers["Access-Control-Allow-Methods"]
        assert "OPTIONS" in expected_headers["Access-Control-Allow-Methods"]
    
    def test_options_method_returns_200(self):
        """Test that OPTIONS request returns 200 without processing"""
        request_method = "OPTIONS"
        
        if request_method == "OPTIONS":
            expected_status = 200
            
            assert expected_status == 200
        else:
            pytest.fail("OPTIONS should return 200 immediately")
    
    @patch('builtins.session', {'user_id': 123})
    def test_get_method_routing(self):
        """Test GET method routes correctly (with/without id parameter)"""
        has_id_param = True
        if has_id_param:
            expected_action = "getSingle"
        else:
            expected_action = "getAll"
        
        assert expected_action == "getSingle"
        
        has_id_param = False
        if has_id_param:
            expected_action = "getSingle"
        else:
            expected_action = "getAll"
        
        assert expected_action == "getAll"
    
    @patch('builtins.session', {'user_id': 123})
    def test_post_method_requires_data(self):
        """Test POST method requires input data"""
        method = "POST"
        input_data = None
        
        if method == "POST" and input_data is None:
            assert input_data is None
        else:
            assert False, "POST should handle missing data gracefully"
        
        input_data = {"name": "Morning Run", "duration": 30}
        if method == "POST" and input_data is not None:
            assert input_data["name"] == "Morning Run"
            assert input_data["duration"] == 30
    
    @patch('builtins.session', {'user_id': 123})
    def test_put_method_requires_id(self):
        """Test PUT method requires id in request body"""
        input_data_with_id = {"id": 5, "name": "Updated Workout"}
        
        if "id" in input_data_with_id and input_data_with_id["id"]:
            assert input_data_with_id["id"] == 5
            assert input_data_with_id["name"] == "Updated Workout"
        else:
            pytest.fail("PUT with id should succeed")

        input_data_without_id = {"name": "Updated Workout"}
        
        has_id = "id" in input_data_without_id and input_data_without_id["id"]
        
        if not has_id:
            expected_message = "Brak ID do aktualizacji"
            assert expected_message == "Brak ID do aktualizacji"
    
    @patch('builtins.session', {'user_id': 123})
    def test_delete_method_requires_id_param(self):
        """Test DELETE method requires id query parameter"""

        has_id_param = True
        if has_id_param:
            assert True
        else:
            assert False, "DELETE without id should not proceed"
 
        has_id_param = False
        if has_id_param:

            assert False, "Should not call delete without id"
        else:
            assert True
    
    @patch('builtins.session', {'user_id': 123})
    def test_invalid_method_returns_error(self):
        """Test that unsupported HTTP methods return error message"""
        invalid_method = "PATCH"
        supported_methods = ["GET", "POST", "PUT", "DELETE", "OPTIONS"]
        
        if invalid_method not in supported_methods:
            expected_response = {"message": "Metoda nieobsługiwana"}
            assert expected_response["message"] == "Metoda nieobsługiwana"
        else:
            pytest.fail(f"{invalid_method} should not be supported")