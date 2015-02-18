/*appLogin
	.constant('AUTH_EVENTS', {
		loginSuccess: 'auth-login-success',
		loginFailed: 'auth-login-failed',
		logoutSuccess: 'auth-logout-success',
		sessionTimeout: 'auth-session-timeout',
		notAuthenticated: 'auth-not-authenticated',
		notAuthorized: 'auth-not-authorized'
	})
	.constant('USER_ROLES', {
		admin: 'admin',
		user: 'user',
		guest: 'guest'
	});

appLogin
.factory('AuthService', function ($http, Session) {
    var authService = {};
    authService.login = function (credentials) {
        return $http.post('/login' + credentials)
            .then(function (res) {
                Session.create(res.data);
            });
    };

    authService.isAuthenticated = function () {
        return !!Session.user;
    };

    authService.isAuthorized = function (authorizedRoles) {
        if (!angular.isArray(authorizedRoles)) {
            authorizedRoles = [authorizedRoles];
        }
        return (authService.isAuthenticated() &&
            authorizedRoles.indexOf(Session.userRole));
    };

    return authService;
}).factory('AuthInterceptor', function ($q, Session) {
    return {
        request: function (config) {
            config.headers['Authorization'] = 'Bearer ' + Session.access_token;
            return config || $q.when(config);
        }
    };
});


appLogin.service('Session', function ($rootScope, $cookieStore, AUTH_EVENTS) {
    var storedSession = $cookieStore.get('userSession');
    var that = this;

    if(storedSession) {
        this.access_token = storedSession.access_token;
        this.user = storedSession.user;
    }

    this.create = function (sessionData) {
        this.access_token = sessionData.access_token;
        this.user = sessionData.user;
        $cookieStore.put('userSession', sessionData);
    };

    this.destroy = function () {
        that.access_token = null;
        that.user = null;
        $cookieStore.remove('userSession');
    };

    $rootScope.$on(AUTH_EVENTS.notAuthenticated, this.destroy);
    $rootScope.$on(AUTH_EVENTS.sessionTimeout, this.destroy);

    return this;
});

appLogin.config(function ($httpProvider) {
    $httpProvider.interceptors.push([
        '$injector',
        function ($injector) {
            return $injector.get('AuthInterceptor');
        }
    ]);
});


appLogin.controller('LoginCtrl', function ($scope, $rootScope, AUTH_EVENTS, AuthService, $location) {
	console.log($rootScope);
    $scope.credentials = {
        username: '',
        password: ''
    };
    $scope.login = function (credentials) {
        AuthService.login(credentials).then(function () {
            $rootScope.$broadcast(AUTH_EVENTS.loginSuccess);
            $location.path('/portal-niase/');
        }, function () {
            $rootScope.$broadcast(AUTH_EVENTS.loginFailed);
        });
    };
});*/


app.controller('HomeCtrl', function($rootScope, $location)
{
   $rootScope.activetab = $location.path();
});
 
app.controller('LoginCtrl', function($rootScope, $location)
{
   $rootScope.activetab = $location.path();
});

app.controller('SeparacaoCtrl', function($rootScope, $location)
{
   $rootScope.activetab = $location.path();
});

app.controller('ConferenciaCtrl', function($rootScope, $location)
{
   $rootScope.activetab = $location.path();
});

app.controller('ExpedicaoCtrl', function($rootScope, $location)
{
   $rootScope.activetab = $location.path();
});