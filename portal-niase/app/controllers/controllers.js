app.factory('httpG', ['$http', '$window', function ($http, $window) {
    var serviceToken, serviceHost, tokenKey;
    tokenKey = 'token';
    if (localStorage.getItem(tokenKey)) {
        serviceToken = $window.localStorage.getItem(tokenKey);
    }

    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

    return {
        setHost: function (host) {
            serviceHost = host;
        },

        setToken: function (token) {
            serviceToken = token;
            $window.localStorage.setItem(tokenKey, token);
        },

        getToken: function () {
            return serviceToken;
        },

        removeToken: function() {
            serviceToken = undefined;
            $window.localStorage.removeItem(tokenKey);
        },

        get: function (uri, params) {
            params = params || {};
            params['_token'] = serviceToken;
            return $.ajax({type: "POST", url: uri, data: params, dataType: 'json'});
        },

        post: function (uri, params) {
            params = params || {};
            params['_token'] = serviceToken;

            return $http.post(serviceHost + uri, params);
        }
    };
}]);

app.controller('MainController', ['$scope', '$location', 'httpG', function ($scope, $location, httpG) {
    
}]);


app.controller('LoginCtrl', ['$scope', '$location', 'httpG', function ($scope, $location, httpG) {
    $scope.login = function(credentials){
        console.log(credentials);
        httpG.get('http://www.webtalk.com.br/uperp/login', credentials).success(function (data) {
            console.log(data);
            if (data.num == 1) {
                //httpG.setToken(data.info.token);
                //$scope.isAuthenticated = true;
                $location.path('/portal-niase/home');
            } else {
                alert(data.msg);
            }
        }).error(function (error) {
            console.log(error);
            alert("Login Error!");
        });
    };

    $scope.doLogOut = function () {
        httpG.removeToken();
    };
}]);




app.controller('HomeCtrl', function($rootScope, $location)
{
   $rootScope.activetab = $location.path();
});
 
app.controller('SeparacaoCtrl', function($rootScope, $location, $scope, $window, $http)
{
    var json;
    $http.get('http://webtalk.com.br/uperp/pedidos/a-separar').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.aSeparar = json;        
    });
    $http.get('http://webtalk.com.br/uperp/pedidos/em-separacao').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.emSeparacao = json;
    });
    $http.get('http://webtalk.com.br/uperp/pedidos/separados').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.separados = json;
    });
    $rootScope.activetab = $location.path();
});



app.controller('ConferenciaCtrl', function($rootScope, $location, $scope, $window, $http)
{
    var json;
    $http.get('http://webtalk.com.br/uperp/conferencia/a-conferir').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.aConferir = json;
    });
    $http.get('http://webtalk.com.br/uperp/conferencia/em-conferencia').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.emConferencia = json;
    });
    $http.get('http://webtalk.com.br/uperp/conferencia/conferidos').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.conferidos = json;
    });
    $rootScope.activetab = $location.path();
});

app.controller('ExpedicaoCtrl', function($rootScope, $location, $scope, $window, $http)
{
    var json;
    $http.get('http://webtalk.com.br/uperp/expedicao/a-embalar').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.aEmbalar = json;
    });
    $http.get('http://webtalk.com.br/uperp/expedicao/embalando').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.embalando = json;
    });
    $http.get('http://webtalk.com.br/uperp/expedicao/embalados').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.embalados = json;
    });
    $rootScope.activetab = $location.path();
});