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

INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
VALUES (null,'maintenance.attachment.download','/maintenance/attachment/{id_attachment}/download','get',null,null,1,1);

INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
VALUES (null,'maintenance.attachment.upload','/maintenance/attachment/upload','post',null,null,1,1);




-- Add base data to initialize package
INSERT INTO public.maintenance_job_category_ref
(job_category_code, job_category_name, job_category_icon, maintenance_job_category_ref_active)
VALUES('NRML', 'normal', '-', 1);


INSERT INTO public.maintenance_job_priority_ref
(priority_code, priority_name, priority_icon, maintenance_job_priority_ref_active)
VALUES('NRML', 'normal', '-', 1);

INSERT INTO public.maintenance_job_status_ref
(job_status_code, job_status_name, job_status_icon, maintenance_job_status_ref_active)
VALUES('OPUN', 'Open_Unassigned', '-', 1),
('OPAS', 'Open_Assigned', '-', 1),
('HOLD', 'On_Hold', '-', 1),
('INPR', 'In_Progress', '-', 1),
('CLOS', 'Closed', '-', 1);


INSERT INTO public.maintenance_job_sla_ref
(id_saas_client_business, id_maintenance_job_priority_ref, id_client, maximum_expected_seen_minutes, expected_target_minutes, maintenance_job_sla_ref_active)
VALUES(1, 1, 1, '30', '3000', 1),
(1, 1, 1, '30', '3000', 1),
(1, 1, 2, '30', '3000', 1),
(1, 1, 3, '30', '3000', 1),
(1, 1, 4, '30', '3000', 1),
(1, 1, 5, '30', '3000', 1),
(1, 1, 6, '30', '3000', 1),
(1, 1, 7, '30', '3000', 1),
(1, 1, 8, '30', '3000', 1),
(1, 1, 10, '30', '3000', 1),
(1, 1, 11, '30', '3000', 1),
(1, 1, 12, '30', '3000', 1),
(1, 1, 13, '30', '3000', 1),
(1, 1, 14, '30', '3000', 1),
(1, 1, 15, '30', '3000', 1),
(1, 1, 16, '30', '3000', 1),
(1, 1, 17, '30', '3000', 1),
(1, 1, 18, '30', '3000', 1),
(1, 1, 19, '30', '3000', 1),
(1, 1, 20, '30', '3000', 1),
(1, 1, 21, '30', '3000', 1),
(1, 1, 22, '30', '3000', 1),
(1, 1, 23, '30', '3000', 1),
(1, 1, 24, '30', '3000', 1),
(1, 1, 25, '30', '3000', 1),
(1, 1, 26, '30', '3000', 1),
(1, 1, 27, '30', '3000', 1);






