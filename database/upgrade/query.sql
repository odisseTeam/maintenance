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
