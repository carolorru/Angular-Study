app.factory('AuthenticationService',
['Base64', '$http', '$cookieStore', '$rootScope', '$timeout', '$location',
function (Base64, $http, $cookieStore, $rootScope, $timeout, $location) {
    var service = {};

    service.Login = function (username, password, callback) {

        /* Use this for real authentication
         ----------------------------------------------*/
        $.post('http://carolineorru.com.br/portal-niazi/services/login', { email: username, pass: password }, 'json').success(function (data) {
            data = JSON.parse(data);            
            console.log('service post', data);
            callback(data);
        }).error(function (error) {
            callback(error);
            //alert("Login Error!");
        });

    };

    service.SetCredentials = function (username, password, permissions, callback) {
        var authdata = Base64.encode(username + ':' + password);

        $rootScope.globals = {
            currentUser: {
                username: username,
                authdata: authdata
            },
            menu: permissions
        };

        //console.log($rootScope.globals);

        $http.defaults.headers.common['Authorization'] = 'Basic ' + authdata; // jshint ignore:line
        //console.log($http.defaults.headers.common['Authorization']);

        $cookieStore.put('globals', $rootScope.globals);
        //console.log($cookieStore);
        callback();
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
})
.factory('Permission', 
['$rootScope', '$location', '$cookieStore', '$http', 
function ($rootScope, $location, $cookieStore, $http){
    var service = {};
    service.validation = function(){
        $rootScope.accessExpedicao = false;
        $rootScope.accessSeparacao = false;
        $rootScope.accessConferencia = false;
        for (var i = 0; i < $rootScope.globals.menu.length; i++) {
            switch($rootScope.globals.menu[i].id) {
                case '1':
                    $rootScope.accessExpedicao = true;
                    break;
                case '2':
                    $rootScope.accessSeparacao = true;
                    break;
                case '3':
                    $rootScope.accessConferencia = true;
                    break;
            }            
        }; 

    }
    return service;
}]);


app.controller('LoginCtrl', ['$scope', '$rootScope', '$location', 'AuthenticationService', function($scope, $rootScope, $location, AuthenticationService) {
    // reset login status
    AuthenticationService.ClearCredentials();
    $scope.login = function() {
        $scope.dataLoading = true;
        AuthenticationService.Login($scope.email, $scope.pass, function(response) {
            //console.log('dentro do callback',response);
            if(response.num == 1) {
                AuthenticationService.SetCredentials($scope.email, $scope.pass, response.menu, function(){
                    $scope.$apply(function() { $location.path("/portal-niazi/home"); });
                });   
            } else {
                $scope.$apply(function() {
                    alert(response.msg);
                    $scope.dataLoading = false;
                });
            }
            
        });        
    };

}]);

app.controller('HomeCtrl', ['$scope', '$rootScope', '$location', 'Permission', function($scope, $rootScope, $location, Permission) {
    Permission.validation();
    $rootScope.activetab = $location.path();
}]);
 
app.controller('SeparacaoCtrl', ['$scope', '$rootScope', '$location', '$http', 'Permission', function($scope, $rootScope, $location, $http, Permission) {
    $scope.viewLoading = true;
    Permission.validation();
    var json;
    
    $http.get('http://carolineorru.com.br/portal-niazi/services/pedidos/a-separar').success(function(data){
        if (data.code == 500) $location.path("/portal-niazi/home"); 
        json = data;
        $scope.aSeparar = json;        
    });
    $http.get('http://carolineorru.com.br/portal-niazi/services/pedidos/em-separacao').success(function(data){
        if (data.code == 500) $location.path("/portal-niazi/home"); 
        json = data;
        $scope.emSeparacao = json;
    });
    $http.get('http://carolineorru.com.br/portal-niazi/services/pedidos/separados').success(function(data){
       if (data.code == 500) $location.path("/portal-niazi/home"); 
        json = data;
        $scope.separados = json;
    }).then(function() {
        $scope.viewLoading = false;
    });
    $rootScope.activetab = $location.path();
}]);



app.controller('ConferenciaCtrl', ['$scope', '$rootScope', '$location', '$http', 'Permission', '$q', function($scope, $rootScope, $location, $http, Permission, $q) {
    $scope.viewLoading = true;
    Permission.validation();
    function getContent() {
        var json;
        $q.when(
            $http.get('http://carolineorru.com.br/portal-niazi/services/conferencia/a-conferir').success(function(data){
                if (data.code == 500) $location.path("/portal-niazi/home"); 
                json = data;
                $scope.aConferir = json;
            }),
            $http.get('http://carolineorru.com.br/portal-niazi/services/conferencia/em-conferencia').success(function(data){
                if (data.code == 500) $location.path("/portal-niazi/home"); 
                json = data;
                $scope.emConferencia = json;
            }),
            $http.get('http://carolineorru.com.br/portal-niazi/services/conferencia/conferidos').success(function(data){
                if (data.code == 500) $location.path("/portal-niazi/home"); 
                json = data;
                $scope.conferidos = json;
            })
        ).then(function() {
                $scope.viewLoading = false;
            });
    };
    getContent();
    $rootScope.activetab = $location.path();
}]);

app.controller('ExpedicaoCtrl', ['$scope', '$rootScope', '$location', '$http', 'Permission', function($scope, $rootScope, $location, $http, Permission) {
    $scope.viewLoading = true;
    Permission.validation();
    var json;
    $http.get('http://carolineorru.com.br/portal-niazi/services/expedicao/a-embarcar').success(function(data){
        if (data.code == 500) $location.path("/portal-niazi/home"); 
        json = data;
        $scope.aEmbalar = json;
    });
    $http.get('http://carolineorru.com.br/portal-niazi/services/expedicao/embarcando').success(function(data){
        if (data.code == 500) $location.path("/portal-niazi/home"); 
        json = data;
        $scope.embalando = json;
    });
    $http.get('http://carolineorru.com.br/portal-niazi/services/expedicao/embarcados').success(function(data){
        if (data.code == 500) $location.path("/portal-niazi/home"); 
        json = data;
        $scope.embalados = json;
    }).then(function() {
        $scope.viewLoading = false;
    });
    $rootScope.activetab = $location.path();
}]);