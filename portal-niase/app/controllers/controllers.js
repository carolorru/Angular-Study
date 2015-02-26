app.factory('AuthenticationService',
    ['Base64', '$http', '$cookieStore', '$rootScope', '$timeout', '$location',
    function (Base64, $http, $cookieStore, $rootScope, $timeout, $location) {
        var service = {};
 
        service.Login = function (username, password, callback) {
 
            /* Use this for real authentication
             ----------------------------------------------*/
            $.post('http://carolineorru.com.br/portal-niase/services/login', { email: username, pass: password }).success(function (data) {
                callback(data);
                console.log(data);
                if (data.num == 1) {
                    $location.path('/portal-niase/home');
                } else {
                    alert(data.msg);
                }
            }).error(function (error) {
                callback(error);
                console.log(error);
                alert("Login Error!");
            });
 
        };
  
        service.SetCredentials = function (username, password) {
            var authdata = Base64.encode(username + ':' + password);
  
            $rootScope.globals = {
                currentUser: {
                    username: username,
                    authdata: authdata
                }
            };
  
            $http.defaults.headers.common['Authorization'] = 'Basic ' + authdata; // jshint ignore:line
            $cookieStore.put('globals', $rootScope.globals);
        };
  
        service.ClearCredentials = function () {
            $rootScope.globals = {};
            $cookieStore.remove('globals');
            $http.defaults.headers.common.Authorization = 'Basic ';
        };
  
        return service;
    }])
  
.factory('Base64', function () {
    /* jshint ignore:start */
  
    var keyStr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
  
    return {
        encode: function (input) {
            var output = "";
            var chr1, chr2, chr3 = "";
            var enc1, enc2, enc3, enc4 = "";
            var i = 0;
  
            do {
                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);
  
                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;
  
                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }
  
                output = output +
                    keyStr.charAt(enc1) +
                    keyStr.charAt(enc2) +
                    keyStr.charAt(enc3) +
                    keyStr.charAt(enc4);
                chr1 = chr2 = chr3 = "";
                enc1 = enc2 = enc3 = enc4 = "";
            } while (i < input.length);
  
            return output;
        },
  
        decode: function (input) {
            var output = "";
            var chr1, chr2, chr3 = "";
            var enc1, enc2, enc3, enc4 = "";
            var i = 0;
  
            // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
            var base64test = /[^A-Za-z0-9\+\/\=]/g;
            if (base64test.exec(input)) {
                window.alert("There were invalid base64 characters in the input text.\n" +
                    "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
                    "Expect errors in decoding.");
            }
            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
  
            do {
                enc1 = keyStr.indexOf(input.charAt(i++));
                enc2 = keyStr.indexOf(input.charAt(i++));
                enc3 = keyStr.indexOf(input.charAt(i++));
                enc4 = keyStr.indexOf(input.charAt(i++));
  
                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;
  
                output = output + String.fromCharCode(chr1);
  
                if (enc3 != 64) {
                    output = output + String.fromCharCode(chr2);
                }
                if (enc4 != 64) {
                    output = output + String.fromCharCode(chr3);
                }
  
                chr1 = chr2 = chr3 = "";
                enc1 = enc2 = enc3 = enc4 = "";
  
            } while (i < input.length);
  
            return output;
        }
    };
  
    /* jshint ignore:end */
});

app.controller('MainController', function($rootScope, $location)
{
});


app.controller('LoginCtrl', ['$scope', '$rootScope', '$location', 'AuthenticationService', function($scope, $rootScope, $location, AuthenticationService) {
    // reset login status
    AuthenticationService.ClearCredentials();
    $scope.login = function () {

        $scope.dataLoading = true;
        AuthenticationService.Login($scope.email, $scope.pass, function(response) {
            if(response.num == 1) {
                AuthenticationService.SetCredentials($scope.email, $scope.pass);
                $location.path('/portal-niase/');
            } else {
                $scope.error = response.message;
                $scope.dataLoading = false;
            }
        });
    };
}]);




app.controller('HomeCtrl', function($rootScope, $location)
{
   $rootScope.activetab = $location.path();
});
 
app.controller('SeparacaoCtrl', function($rootScope, $location, $scope, $window, $http)
{
    var json;
    $http.get('http://carolineorru.com.br/portal-niase/services/pedidos/a-separar').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.aSeparar = json;        
    });
    $http.get('http://carolineorru.com.br/portal-niase/services/pedidos/em-separacao').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.emSeparacao = json;
    });
    $http.get('http://carolineorru.com.br/portal-niase/services/pedidos/separados').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.separados = json;
    });
    $rootScope.activetab = $location.path();
});



app.controller('ConferenciaCtrl', function($rootScope, $location, $scope, $window, $http)
{
    var json;
    $http.get('http://carolineorru.com.br/portal-niase/services/conferencia/a-conferir').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.aConferir = json;
    });
    $http.get('http://carolineorru.com.br/portal-niase/services/conferencia/em-conferencia').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.emConferencia = json;
    });
    $http.get('http://carolineorru.com.br/portal-niase/services/conferencia/conferidos').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.conferidos = json;
    });
    $rootScope.activetab = $location.path();
});

app.controller('ExpedicaoCtrl', function($rootScope, $location, $scope, $window, $http)
{
    var json;
    $http.get('http://carolineorru.com.br/portal-niase/services/expedicao/a-embalar').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.aEmbalar = json;
    });
    $http.get('http://carolineorru.com.br/portal-niase/services/expedicao/embalando').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.embalando = json;
    });
    $http.get('http://carolineorru.com.br/portal-niase/services/expedicao/embalados').success(function(data){
        if (data.code == 500) $location.path('/');
        json = data;
        $scope.embalados = json;
    });
    $rootScope.activetab = $location.path();
});