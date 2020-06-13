/*
 * OnService.js v0.0.1 
 * 2018 - Wallace Rio - wallrio@gmail.com
 */
var OnService = (function(){
	function OnService(Modules){	

		this.version = '0.0.1';

		this.config = {
			domain:''
		};
	
		this.attachModule = function(ModuleInject){

			var module =  ModuleInject;

			OnService.prototype[ (new module).name ] = module;			
		}

	}	
	return new OnService;
})();

