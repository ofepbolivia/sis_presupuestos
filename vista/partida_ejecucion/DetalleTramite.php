
<script>    
    Phx.vista.DetalleTramite=Ext.extend(Phx.gridInterfaz,{        
        constructor: function(config) {
            this.maestro = config;                                    
            
            Phx.vista.DetalleTramite.superclass.constructor.call(this,config); 
            console.log('data=>',this.maestro);
                       
            var des = (this.maestro.desde == null )? '':this.maestro.desde.dateFormat('d/m/Y');
            var has =  (this.maestro.hasta == null )? '':this.maestro.hasta.dateFormat('d/m/Y');            
            this.init();                                    
            this.load({params:{start:0, limit:this.tam_pag,nro_tramite:this.maestro.nro_tramite, id_presupuesto: this.maestro.id_presupuesto, id_partida:this.maestro.id_partida, desde:des, hasta:has}});            
        },
        bactGroups:[],
        bexcelGroups:[],
        gruposBarraTareas: [
            {name:  'comprometido', title: '<h1 style="text-align: center; color: brown ;"><i class="fa fa-circle-o" aria-hidden="true"></i>COMPROMETIDO</h1>',grupo: 0, height: 0} ,
            {name: 'ejecutado', title: '<h1 style="text-align: center; color: red;"><i  class="fa fa-circle" aria-hidden="true"></i>EJECUTADO</h1>', grupo: 1, height: 1},
            {name: 'pagado', title: '<h1 style="text-align: center; color: green;"><i class="fa fa-dot-circle-o" aria-hidden="true"></i>PAGADO</h1>', grupo: 2, height: 2}
        ],        
        actualizarSegunTab: function(name, indice){                                            
            var des = (this.maestro.desde == null )? '':this.maestro.desde.dateFormat('d/m/Y');
            var has= (this.maestro.hasta == null )? '':this.maestro.hasta.dateFormat('d/m/Y');            
            switch (name) {
                case 'comprometido':                     
                    this.ocultarColumnaByName('ejecutado');
                    this.ocultarColumnaByName('pagado');
                    this.mostrarColumnaByName('comprometido');
                    this.store.baseParams.estado_func = name;                  
                    break;            
                case 'ejecutado':                        
                    this.ocultarColumnaByName('comprometido');
                    this.ocultarColumnaByName('pagado');
                    this.mostrarColumnaByName('ejecutado');
                    this.store.baseParams.estado_func = name;
                    break;
                case 'pagado':                             
                    this.ocultarColumnaByName('comprometido');
                    this.ocultarColumnaByName('ejecutado');
                    this.mostrarColumnaByName('pagado');
                    this.store.baseParams.estado_func = name;
                break;                    
            }                                                
            this.load({params:{start:0, limit:this.tam_pag,nro_tramite:this.maestro.nro_tramite, id_presupuesto: this.maestro.id_presupuesto, id_partida:this.maestro.id_partida, desde:des, hasta:has}});
        },        
        Atributos:[
            {
                //configuracion del componente
                config:{
                    labelSeparator:'',
                    inputType:'hidden',
                    name: 'id_partida_ejecucion'
                },
                type:'Field',
                form:true
            },
            {
                config:{
                    labelSeparator:'',
                    name: 'id_partida_ejecucion_fk',
                    fieldLabel: 'id_partida_ejecucion_fk',
                    inputType:'hidden'
                },
                type:'Field',
                form:true
            },            
            {
                config:{
                    name: 'fecha',
                    fieldLabel: 'Fecha Ejecucion',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    col : this.color,
                    renderer: (value,p,record) => {                        
                    if(record.data.tipo_reg != 'summary'){
                            return value?value.dateFormat('d/m/Y'):''                        
                    }else{
                        return `<hr><center><b><p style=" color:green; font-size:15px;">Total: </p></b></center>`;
                    }
                 }
                },
                type:'DateField',
                filters:{pfiltro:'pej.fecha',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
		    },            
            {
                config:{
                    name: 'comprometido',                    
                    currencyChar:' ',
                    fieldLabel: 'Comprometido',
                    allowBlank: false,
                    anchor: '80%',
                    gwidth: 100,                    
                    renderer: (value, p, record) => {                                                
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('<div style="text-align:right;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                        }else{
                            return  String.format('<hr><div style="font-size:15px; float:right; color:black;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_comprometido,'0.000,00/i'));
                        }
                    }
                },
                type:'MoneyField',
                filters:{pfiltro:'comprometido',type:'numeric'},
                id_grupo:0,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'ejecutado',                    
                    fieldLabel: 'Ejecutado',                    
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,            
                    renderer: (value, p, record) => {                                                
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('<div style="text-align:right;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                        }else{
                            return  String.format('<hr><div style="font-size:15px; float:right; color:black;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_ejecutado,'0.000,00/i'));
                        }
                    }
                },
                type:'MoneyField',
                filters:{pfiltro:'ejecutado',type:'numeric'},
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'pagado',
                    fieldLabel: 'Pagado',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    renderer: (value, p, record) => {                                                
                        if(record.data.tipo_reg != 'summary'){
                            return  String.format('<div style="text-align:right;">{0}</div>', Ext.util.Format.number(value,'0.000,00/i'));
                        }else{
                            return  String.format('<hr><div style="font-size:15px; float:right; color:black;"><b><font>{0}</font><b></div>', Ext.util.Format.number(record.data.total_pagado,'0.000,00/i'));
                        }
                    }                    
                },
                type:'NumberField',
                filters:{pfiltro:'pagado',type:'numeric'},
                id_grupo:2,
                grid:true,
                form:true
            },                        
            {
                config:{
                    name: 'moneda',
                    fieldLabel: 'Moneda',
                    allowBlank: true,
                    anchor: '70%',
                    gwidth: 100                    
                },
                type:'TextField',
                filters:{pfiltro:'moneda',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },           
            {
                config:{
                    name: 'codigo_cc',
                    //name: 'desc_pres',
                    fieldLabel: 'Desc. Presupuesto',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200
                },
                type:'TextField',
                //filters:{pfiltro:'pre.descripcion',type:'string'},
                filters:{pfiltro:'vpre.codigo_cc',type:'string'},
                id_grupo:1,
                bottom_filter: true,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'codigo_categoria',
                    fieldLabel: 'Código Categoría Programatica',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 150,
                    maxLength:1000
                },
                type:'TextField',
                //filters:{pfiltro:'pareje.nro_tramite',type:'string'},
                filters:{pfiltro:'cat.codigo_categoria',type:'string'},
                bottom_filter: true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'codigo',
                    fieldLabel: 'Codigo Partida',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 60,
                    maxLength:-5
                },
                type:'TextField',
                filters:{pfiltro:'par.codigo',type:'string'},
                bottom_filter: true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'nombre_partida',
                    fieldLabel: 'Nombre Partida',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 200                    
                },
                type:'TextField',
                filters:{pfiltro:'par.nombre_partida',type:'string'},
                bottom_filter: true,
                id_grupo:1,
                grid:true,
                form:true
            },
            {
                config:{
                    name: 'estado_reg',
                    fieldLabel: 'Estado Reg.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100                    
                },
                type:'TextField',
                filters:{pfiltro:'obdet.estado_reg',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_reg',
                    fieldLabel: 'Fecha creación',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'obdet.fecha_reg',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_reg',
                    fieldLabel: 'Creado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'NumberField',
                filters:{pfiltro:'usu1.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'fecha_mod',
                    fieldLabel: 'Fecha Modif.',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
                },
                type:'DateField',
                filters:{pfiltro:'obdet.fecha_mod',type:'date'},
                id_grupo:1,
                grid:true,
                form:false
            },
            {
                config:{
                    name: 'usr_mod',
                    fieldLabel: 'Modificado por',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 100,
                    maxLength:4
                },
                type:'NumberField',
                filters:{pfiltro:'usu2.cuenta',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            }                                          
        ],
        tam_pag:50,
        title:'Detalle',                        
        ActList:'../../sis_presupuestos/control/PartidaEjecucion/listarDetalleTramite',
        id_store:'id_partida_ejecucion',
        fields: [
            {name:'id_partida_ejecucion', type: 'numeric'},
            {name:'id_partida_ejecucion_fk', type: 'numeric'},
            {name:'moneda', type: 'string'},
            {name:'comprometido', type: 'numeric'},            
            {name:'ejecutado', type: 'numeric'},  
            {name:'pagado', type: 'numeric'},  
            {name:'nro_tramite', type: 'string'},
            {name:'nombre_partida', type: 'string'},
            {name:'codigo', type: 'string'},
            {name:'codigo_categoria', type:'string'},
            {name:'fecha', type: 'date',dateFormat:'Y-m-d'},
            {name:'codigo_cc', type:'string'},
            {name:'usr_reg', type: 'string'},
            {name:'usr_mod', type: 'string'},
            {name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},            
            {name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
            {name:'estado_reg', type: 'string'},
            {name:'total_comprometido', type: 'numeric'},
            {name:'total_ejecutado', type: 'numeric'},
            {name:'total_pagado', type: 'numeric'},
            {name:'tipo_reg', type: 'string'}
        ],
        sortInfo:{
            field: 'id_partida_ejecucion',
            direction: 'ASC'
        }, 
        bedit: false,
        bnew:  false,
        bdel:  false,
        bsave: false,        
        btest: false,                
        fwidth: '90%',
        fheight: '95%',        
        loadValoresIniciales:function()
        {	                                    
            Phx.vista.DetalleTramite.superclass.loadValoresIniciales.call(this);
        }        

    });
</script>
