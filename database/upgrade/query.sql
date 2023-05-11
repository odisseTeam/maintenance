INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('contractor.show_create_page','/maintenance/contractor','get',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('contractor.show_mgt_page','/maintenance/contractors','get',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('contractor.show_edit_page','/maintenance/contractor/{id_contractor}','get',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('contractor.store','/maintenance/contractor','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('contractor.update','/maintenance/contractor/{id_contractor}','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('contractors.get','/maintenance/contractors','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('contractor.delete','/maintenance/contractor/delete/{id_contractor}','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('maintenance.dashboard','/maintenance/dashboard','get',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('maintenance.list','/maintenance/maintenances_list','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('maintenance.delete','/maintenance/delete/{id_maintenance}','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('business_contractor.user_agent','/maintenance/business_contractor/user_agents','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('maintenance.assign_user','/maintenance/assign_user','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('contractor.login_setting.change','/maintenance/contractor/login_settings/change','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('contractor.email.get','/maintenance/contractor/email/{id_contractor}','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('maintenance.management','/maintenance/management','get',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('maintenance.management.list','/maintenance/mgt_maintenances_list','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('maintenance.management.details','/maintenancelist_details','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('mgt.maintenance.delete','/maintenance/mgt/delete/{id_maintenance}','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('mgt.business_contractor','/maintenance/mgt/business_contractors','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('mgt.business_contractor.user_agent','/maintenance/mgt/business_contractor/user_agents','post',1,1);
INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('mgt.maintenance.assign_user','/maintenance/mgt/assign_user','post',1,1);
INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
VALUES (null,'maintenance.get.detail','/maintenance/detail/{maintenanceId}','get',null,null,1,1);
