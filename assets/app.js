import angular from 'angular';
import './styles/app.css';
import 'admin-lte';
import 'overlayscrollbars/styles/overlayscrollbars.css';
import 'bootstrap';
import "angular-ui-bootstrap";
import './services/partituras-services';

import UsuarioController from './controllers/user/user-controller';
import MenuController from "./controllers/menu/menu-controller";
import RoleController from "./controllers/security/role-controller";
import GroupController from "./controllers/security/group-controller";

angular.module('MyApp', ['ui.bootstrap', 'partituras.services'])
    .controller('UsuarioController', UsuarioController)
    .controller('MenuController', MenuController)
    .controller('RoleController', RoleController)
    .controller('GroupController', GroupController)

