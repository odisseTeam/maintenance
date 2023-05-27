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



INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
VALUES (null,'contractor.skills.get','/maintenance/contractor/skill/{id_contractor}','post',null,null,1,1);

INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
VALUES (null,'contractor.skills.change','/maintenance/contractor/skills/change','post',null,null,1,1);



INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
VALUES (null,'contractor.locations.get','/maintenance/contractor/location/{id_contractor}','post',null,null,1,1);


INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
VALUES (null,'contractor.locations.change','/maintenance/contractor/locations/change','post',null,null,1,1);

INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
VALUES (null,'mgt.maintenance.create','/maintenance/mgt/create','get',null,null,1,1);


INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
VALUES (null,'maintenance.start','/maintenance/start/{id_maintenance}','post',null,null,1,1);


INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
VALUES (null,'maintenance.end','/maintenance/end/{id_maintenance}','post',null,null,1,1);

INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('mgt.maintenance.start','/maintenance/mgt/start/{id_maintenance}','post',1,1);

INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('mgt.maintenance.end','/maintenance/mgt/end/{id_maintenance}','post',1,1);

INSERT INTO permission_route_mapping(permission,route,method,mapping_active,id_permission_category)
VALUES ('maintenance.status.chart','/maintenance/statuses/charts','post',1,1);




-- Add base data to initialize package
INSERT INTO public.maintenance_job_category_ref
(job_category_code, job_category_name, job_category_icon, maintenance_job_category_ref_active)
VALUES('ELCT', 'Electrical', '-', 1),
('WATR', 'Water', '-', 1),
('GAS', 'Gas', '-', 1),
('STRC', 'Structural', '-', 1),
('MISC', 'Misc', '-', 1);



INSERT INTO public.maintenance_job_priority_ref
(priority_code, priority_name, priority_icon, maintenance_job_priority_ref_active)
VALUES('low', 'Low', '-', 1),
('mdm', 'Medium', '-', 1),
('high', 'High', '-', 1),
('ugnt', 'Urgent', '-', 1);

INSERT INTO public.maintenance_job_status_ref
(job_status_code, job_status_name, job_status_icon, maintenance_job_status_ref_active)
VALUES('OPUN', 'Open Unassigned', '-', 1),
('OPAS', 'Open Assigned', '-', 1),
('HOLD', 'On Hold', '-', 1),
('INPR', 'In Progress', '-', 1),
('CLOS', 'Closed', '-', 1);


INSERT INTO public.maintenance_job_sla_ref
(id_saas_client_business, id_maintenance_job_priority_ref, id_client, maximum_expected_seen_minutes, expected_target_minutes, maintenance_job_sla_ref_active)VALUES
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
(1, 1, 27, '30', '3000', 1),
(1, 1, null, '30', '3000', 1),


(1, 2, 1, '30', '3000', 1),
(1, 2, 2, '30', '3000', 1),
(1, 2, 3, '30', '3000', 1),
(1, 2, 4, '30', '3000', 1),
(1, 2, 5, '30', '3000', 1),
(1, 2, 6, '30', '3000', 1),
(1, 2, 7, '30', '3000', 1),
(1, 2, 8, '30', '3000', 1),
(1, 2, 10, '30', '3000', 1),
(1, 2, 11, '30', '3000', 1),
(1, 2, 12, '30', '3000', 1),
(1, 2, 13, '30', '3000', 1),
(1, 2, 14, '30', '3000', 1),
(1, 2, 15, '30', '3000', 1),
(1, 2, 16, '30', '3000', 1),
(1, 2, 17, '30', '3000', 1),
(1, 2, 18, '30', '3000', 1),
(1, 2, 19, '30', '3000', 1),
(1, 2, 20, '30', '3000', 1),
(1, 2, 21, '30', '3000', 1),
(1, 2, 22, '30', '3000', 1),
(1, 2, 23, '30', '3000', 1),
(1, 2, 24, '30', '3000', 1),
(1, 2, 25, '30', '3000', 1),
(1, 2, 26, '30', '3000', 1),
(1, 2, 27, '30', '3000', 1),
(1, 2, null, '30', '3000', 1),


(1, 3, 1, '30', '3000', 1),
(1, 3, 2, '30', '3000', 1),
(1, 3, 3, '30', '3000', 1),
(1, 3, 4, '30', '3000', 1),
(1, 3, 5, '30', '3000', 1),
(1, 3, 6, '30', '3000', 1),
(1, 3, 7, '30', '3000', 1),
(1, 3, 8, '30', '3000', 1),
(1, 3, 10, '30', '3000', 1),
(1, 3, 11, '30', '3000', 1),
(1, 3, 12, '30', '3000', 1),
(1, 3, 13, '30', '3000', 1),
(1, 3, 14, '30', '3000', 1),
(1, 3, 15, '30', '3000', 1),
(1, 3, 16, '30', '3000', 1),
(1, 3, 17, '30', '3000', 1),
(1, 3, 18, '30', '3000', 1),
(1, 3, 19, '30', '3000', 1),
(1, 3, 20, '30', '3000', 1),
(1, 3, 21, '30', '3000', 1),
(1, 3, 22, '30', '3000', 1),
(1, 3, 23, '30', '3000', 1),
(1, 3, 24, '30', '3000', 1),
(1, 3, 25, '30', '3000', 1),
(1, 3, 26, '30', '3000', 1),
(1, 3, 27, '30', '3000', 1),
(1, 3, null, '30', '3000', 1),


(1, 4, 1, '30', '3000', 1),
(1, 4, 1, '30', '3000', 1),
(1, 4, 2, '30', '3000', 1),
(1, 4, 3, '30', '3000', 1),
(1, 4, 4, '30', '3000', 1),
(1, 4, 5, '30', '3000', 1),
(1, 4, 6, '30', '3000', 1),
(1, 4, 7, '30', '3000', 1),
(1, 4, 8, '30', '3000', 1),
(1, 4, 10, '30', '3000', 1),
(1, 4, 11, '30', '3000', 1),
(1, 4, 12, '30', '3000', 1),
(1, 4, 13, '30', '3000', 1),
(1, 4, 14, '30', '3000', 1),
(1, 4, 15, '30', '3000', 1),
(1, 4, 16, '30', '3000', 1),
(1, 4, 17, '30', '3000', 1),
(1, 4, 18, '30', '3000', 1),
(1, 4, 19, '30', '3000', 1),
(1, 4, 20, '30', '3000', 1),
(1, 4, 21, '30', '3000', 1),
(1, 4, 22, '30', '3000', 1),
(1, 4, 23, '30', '3000', 1),
(1, 4, 24, '30', '3000', 1),
(1, 4, 25, '30', '3000', 1),
(1, 4, 26, '30', '3000', 1),
(1, 4, 27, '30', '3000', 1),
(1, 4, null, '30', '3000', 1);



INSERT INTO public.contractor_skill_ref
(skill_name, contractor_skill_ref_active)VALUES
('Handyman Services', 1),
('Plumbing Services', 1),
('Rescue Services', 1),
('Electrical Services', 1),
('Security Services', 1),
('Cleaning Services', 1),
('Garden Services', 1),
('Fire Protection Services', 1),
('Domestics Services', 1),
('Repairs Services', 1),
('Synergy Services', 1),
('Furniture Restoration Services', 1),
('Lift Maintenance Services', 1),
('Doors & Windows Services', 1),
('Appliance Repair Services', 1),
('Carpet Cleaning Services', 1),
('Drainage Services', 1);

INSERT INTO public.contractor_location_ref
(location, contractor_location_ref_active)VALUES
('Hampshire', 1),
('St Leonards On Sea', 1),
('Eastbourne', 1),
('Sussex', 1),
('Shoreham', 1),
('Worthing', 1);
