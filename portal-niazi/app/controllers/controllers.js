app.factory('AuthenticationService',
['Base64', '$http', '$cookieStore', '$rootScope', '$timeout', '$location',
function (Base64, $http, $cookieStore, $rootScope, $timeout, $location) {
    var service = {};

    service.Login = function (username, password, callback) {

        /* Use this for real authentication
         ----------------------------------------------*/
        $.post('/portal-niazi/services/login', { email: username, pass: password }, 'json').success(function (data) {
            data = JSON.parse(data);            
            //console.log('service post', data);
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
        console.log($rootScope.globals);
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
    console.log('permissions');
    service.validation = function(){
        $rootScope.accessExpedicao = false;
        $rootScope.accessSeparacao = false;
        $rootScope.accessConferencia = false;
        $rootScope.accessCadastro = true;
        $rootScope.accessConsulta = true;
        for (var i = 0; i < $rootScope.globals.menu.length; i++) {
            console.log($rootScope.globals.menu[i].id);
            switch($rootScope.globals.menu[i].id) {
                case 1:
                    $rootScope.accessExpedicao = true;
                    //console.log('menu1');
                    break;
                case 2:
                    $rootScope.accessSeparacao = true;
                    //console.log('menu2');
                    break;
                case 3:
                    $rootScope.accessConferencia = true;
                    //console.log('menu3');
                    break;
                //case 4:
                //    $rootScope.accessCadastro = true;
                //    console.log('menu4');
                //    break;
            }            
        }; 
    }
    return service;
}])
.factory('currentDate', function(){
    var formatDate = {};

	formatDate.yyyyMMdd = function(i){
		var today = i;
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();

		if(dd<10) {
			dd='0'+dd
		} 

		if(mm<10) {
			mm='0'+mm
		} 
		return yyyy+''+mm+''+dd;
	}

    return formatDate;
})
.factory('Datepicker', function () {
	var datepicker = {};
	
	datepicker.dt = new Date();

	datepicker.open = function($event) {
		$event.preventDefault();
		$event.stopPropagation();

		this.opened = true;
	};
	
	return datepicker;
});

app.directive( 'passwordVerify', function() {
	return{
		require: "ngModel",
		scope: {
			passwordVerify: '='
		},
		link: function(scope, element, attrs, ctrl) {
			scope.$watch(function() {
				var combined;
				
				if (scope.passwordVerify || ctrl.$viewValue){
					combined = scope.passwordVerify + '_' + ctrl.$viewValue;
				}
				return combined;
			}, function(value) {
				if(value){
					ctrl.$parsers.unshift(function(viewValue) {
						var origin = scope.passwordVerify;
						if(origin !== viewValue) {
							ctrl.$setValidity("passwordVerify", false);
							return undefined;
						}else{
							ctrl.$setValidity("passwordVerify", true);
							return viewValue;
						}
					});
				}
			});
		}
	};
});

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

app.controller('HomeCtrl', ['$scope', '$rootScope', '$location', 'Permission', 'Datepicker', function($scope, $rootScope, $location, Permission, Datepicker) {
    Permission.validation();
    $rootScope.activetab = $location.path();
}]);

app.controller('CadastroCtrl', ['$scope', '$rootScope', '$location', '$http', 'Permission', function($scope, $rootScope, $location, $http, Permission) {
    console.log('cadastro');
    Permission.validation();
    $rootScope.activetab = $location.path();
	
	$scope.userName = $rootScope.globals.currentUser.username;
	
	$scope.editPass = function() {
		$http.get('/portal-niazi/services/usuarios/troca-senha?TYPE=MSSQL&pass='+$scope.dataPass.newPass+'&id=1').success(function(data){
            if (data.code == 500) $location.path("/portal-niazi/"); 
			if (data.code == 200) {
				$scope.successForm = true;
				$scope.successMsg = data.msg;
			}
			
        });
	}
	
}]);
 
app.controller('SeparacaoCtrl', ['$scope', '$rootScope', '$location', '$http', 'Permission', 'currentDate', '$interval', 'Datepicker', function($scope, $rootScope, $location, $http, Permission, currentDate, $interval, Datepicker) {
    Permission.validation();
    var counter;
	
    $scope.viewLoading = true;
    $rootScope.activetab = $location.path();
	
	$scope.dt = Datepicker.dt;
	$scope.open = Datepicker.open;
	
	$scope.maxDate = $scope.maxDate ? null : new Date();
	
	$scope.dateOptions = {
		formatYear: 'yy',
		startingDay: 1
	};
	$scope.formats = ['dd-MMMM-yyyy', 'dd/MM/yyyy', 'dd.MM.yyyy', 'shortDate'];
	$scope.format = $scope.formats[1];    

    $scope.getSeparacao = function(index) {
		
        if(index == 0) $scope.viewLoading = true;
        counter = 3;
		var hiddenDate = currentDate.yyyyMMdd($scope.dt);
		//console.log(hiddenDate);
		//$scope.dt.getFullYear().toString() + ($scope.dt.getMonth() + 1).toString() + $scope.dt.getDate().toString();
        
		var json;
        $http.get('/portal-niazi/services/pedidos/a-separar?TYPE=MSSQL&ref-date='+hiddenDate).success(function(data){
            if (data.code == 500) $location.path("/portal-niazi/"); 
            json = data;
            $scope.aSeparar = json;    
            sucssesAjax(index);    
        });
        $http.get('/portal-niazi/services/pedidos/em-separacao?TYPE=MSSQL&ref-date='+hiddenDate).success(function(data){
            if (data.code == 500) $location.path("/portal-niazi/"); 
            json = data;
            $scope.emSeparacao = json;
            sucssesAjax(index);
        });
        $http.get('/portal-niazi/services/pedidos/separados?TYPE=MSSQL&ref-date='+hiddenDate).success(function(data){
		console.log(data)
           if (data.code == 500) $location.path("/portal-niazi/"); 
            json = data;
            $scope.separados = json;
            sucssesAjax(index);
        });
    }

    function sucssesAjax(index) {
        counter --;
        if (counter === 0 && index == 0) {
            $scope.viewLoading = false;
        }
    } 
	
	$scope.getSeparacao(0);

    var intervalContent = $interval(function(){
        $scope.getSeparacao(1);    
    },45000);
    $scope.$on('$destroy', function () { $interval.cancel(intervalContent); });
}]);



app.controller('ConferenciaCtrl', ['$scope', '$rootScope', '$location', '$http', 'Permission', 'currentDate', '$interval', 'Datepicker', function($scope, $rootScope, $location, $http, Permission, currentDate, $interval, Datepicker) {
    Permission.validation();
    var counter;

    $scope.viewLoading = true;
    $rootScope.activetab = $location.path();
	
	$scope.dt = Datepicker.dt;
	$scope.open = Datepicker.open;
	
	$scope.maxDate = $scope.maxDate ? null : new Date();
	
	$scope.dateOptions = {
		formatYear: 'yy',
		startingDay: 1
	};
	$scope.formats = ['dd-MMMM-yyyy', 'dd/MM/yyyy', 'dd.MM.yyyy', 'shortDate'];
	$scope.format = $scope.formats[1]; 

    $scope.getConferencia = function(index) {
        if(index == 0) $scope.viewLoading = true;
        counter = 3;
		var hiddenDate = currentDate.yyyyMMdd($scope.dt);
        var json;
        $http.get('/portal-niazi/services/conferencia/a-conferir?TYPE=MSSQL&ref-date='+hiddenDate).success(function(data){
            if (data.code == 500) $location.path("/portal-niazi/"); 
            json = data;
            $scope.aConferir = json;
            sucssesAjax(index);
        });
        $http.get('/portal-niazi/services/conferencia/em-conferencia?TYPE=MSSQL&ref-date='+hiddenDate).success(function(data){
            if (data.code == 500) $location.path("/portal-niazi/"); 
            json = data;
            $scope.emConferencia = json;
            sucssesAjax(index);
        });
        $http.get('/portal-niazi/services/conferencia/conferidos?TYPE=MSSQL&ref-date='+hiddenDate).success(function(data){
            if (data.code == 500) $location.path("/portal-niazi/"); 
            json = data;
            $scope.conferidos = json;
            sucssesAjax(index);
        });
    
    }
    
    function sucssesAjax(index) {
        counter --;
        if (counter === 0 && index == 0) {
            $scope.viewLoading = false;
        }
    }
	
	$scope.getConferencia(0);

    var intervalContent = $interval(function(){
        $scope.getConferencia(1);    
    },45000);
    $scope.$on('$destroy', function () { $interval.cancel(intervalContent); });
}]);

app.controller('ExpedicaoCtrl', ['$scope', '$rootScope', '$location', '$http', 'Permission', 'currentDate', '$interval', 'Datepicker', function($scope, $rootScope, $location, $http, Permission, currentDate, $interval, Datepicker) {
    Permission.validation();
    var counter;

    $scope.viewLoading = true;
    $rootScope.activetab = $location.path();
	
	$scope.dt = Datepicker.dt;
	$scope.open = Datepicker.open;
	
	$scope.maxDate = $scope.maxDate ? null : new Date();
	
	$scope.dateOptions = {
		formatYear: 'yy',
		startingDay: 1
	};
	$scope.formats = ['dd-MMMM-yyyy', 'dd/MM/yyyy', 'dd.MM.yyyy', 'shortDate'];
	$scope.format = $scope.formats[1];     

    $scope.getExpedicao = function(index) {
        if(index == 0) $scope.viewLoading = true;
        counter = 2;
		var hiddenDate = currentDate.yyyyMMdd($scope.dt);

        var json;
        $http.get('/portal-niazi/services/expedicao/a-embarcar?TYPE=MSSQL&ref-date='+hiddenDate).success(function(data){
            if (data.code == 500) $location.path("/portal-niazi/"); 
            json = data;
			console.log(json);
            $scope.aEmbalar = json;
            sucssesAjax(index);
        });
        $http.get('/portal-niazi/services/expedicao/embarcados?TYPE=MSSQL&ref-date='+hiddenDate).success(function(data){
            if (data.code == 500) $location.path("/portal-niazi/"); 
            json = data;
            $scope.embalados = json;
            sucssesAjax(index);
        });
    }

    function sucssesAjax(index) {
        counter --;
        if (counter === 0 && index == 0) {
            $scope.viewLoading = false;
        }
    }
	
	$scope.getExpedicao(0);

    var intervalContent = $interval(function(){
        $scope.getExpedicao(1);    
    },45000);
    $scope.$on('$destroy', function () { $interval.cancel(intervalContent); });
}]);

app.controller('ConsultaCtrl', ['$scope', '$rootScope', '$location', 'Permission', function($scope, $rootScope, $location, Permission) {
    Permission.validation();
    $rootScope.activetab = $location.path();

    $scope.searchItens = function() {
        $scope.viewLoading = true;
        $.post('/portal-niazi/services/consultas', { filter_where: $scope.SearchApp.filter_where, filter_q: $scope.SearchApp.filter_q }, 'json').success(function (data) {
            data = JSON.parse(data);            
            console.log('service post', data);
            callback(data);
        }).error(function (error) {
            callback(error);
            alert("Login Error!");
        });
    }
}]);