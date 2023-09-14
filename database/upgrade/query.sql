-- all routes

-- route related to contractor update page

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'contractor.attachments.get','/maintenance/contractor/attachments/{id_contractor}','post',null,null,1,1);




-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'contractor.attachment.upload','/maintenance/contractor_file/upload','post',null,null,1,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'contractor.attachment.download','/maintenance/contractor_attachment/{id_attachment}/download','get',null,null,1,1);




-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'contractor.attachment.delete','/maintenance/contractor_document/delete','post',null,null,1,1);




-- -- routes related to create maintenance page
-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.create_page','/maintenance/create/page','get',null,null,1,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.upload_file','/maintenance/upload/file','post',null,null,1,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.maintenance_title','/maintenance/find/maintenance_title','post',null,null,1,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.save_new','/maintenance/new/save','post',null,null,1,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.get.resident_reporter','/maintenance/get/resident_reporter','post',null,null,1,1);

-- --routes related to maintenance detail page
-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.get.detail','/maintenance/detail/{maintenanceId}','get',null,null,1,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.detail.edit','/maintenance/detail/edit','post',null,null,1,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.get.documents','/maintenance/documents/get','post',null,null,1,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.delete.maintenance_document','/maintenance/maintenance_document/delete','post',null,null,1,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.get.timeline','/maintenance/timeline/get','post',null,null,1,1);




-- -- route related to room page (api from package)
-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.api.get_maintenance_list_history','/maintenance/api/maintenancelist_history','post',null,null,1,1);




-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.api.get_maintenance_list_history','/maintenance/api/maintenancelist_history','post',null,null,1,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.attachment.upload','/maintenance/attachment/upload','post',null,null,1,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.attachment.download','/maintenance/attachment/{id_attachment}/download','get',null,null,1,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.api.get_maintenance_list_history','/maintenance/api/maintenancelist_history','post',null,null,1,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.attachment.upload','/maintenance/attachment/upload','post',null,null,1,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active,id_permission_category)
-- VALUES (null,'maintenance.attachment.download','/maintenance/attachment/{id_attachment}/download','get',null,null,1,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
--     VALUES (null,'maintenance.get.resident_reporter','/maintenance/get/resident_reporter','post',null,null,1);


-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('contractor.show_create_page','/maintenance/contractor','get',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('contractor.show_mgt_page','/maintenance/contractors','get',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('contractor.show_edit_page','/maintenance/contractor/{id_contractor}','get',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('contractor.store','/maintenance/contractor','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('contractor.update','/maintenance/contractor/{id_contractor}','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('contractors.get','/maintenance/contractors','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('contractor.delete','/maintenance/contractor/delete/{id_contractor}','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('maintenance.dashboard','/maintenance/dashboard','get',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('maintenance.list','/maintenance/maintenances_list','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('maintenance.delete','/maintenance/delete/{id_maintenance}','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('business_contractor.user_agent','/maintenance/business_contractor/user_agents','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('maintenance.assign_user','/maintenance/assign_user','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('contractor.login_setting.change','/maintenance/contractor/login_settings/change','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('contractor.email.get','/maintenance/contractor/email/{id_contractor}','post',1);



-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('maintenance.management','/maintenance/management','get',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('maintenance.management.list','/maintenance/mgt_maintenances_list','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('maintenance.management.details','/maintenancelist_details','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('mgt.maintenance.delete','/maintenance/mgt/delete/{id_maintenance}','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('mgt.business_contractor','/maintenance/mgt/business_contractors','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('mgt.business_contractor.user_agent','/maintenance/mgt/business_contractor/user_agents','post',1);
-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('mgt.maintenance.assign_user','/maintenance/mgt/assign_user','post',1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.get.detail','/maintenance/detail/{maintenanceId}','get',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.attachment.download','/maintenance/attachment/{id_attachment}/download','get',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.attachment.upload','/maintenance/attachment/upload','post',null,null,1);



-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'contractor.skills.get','/maintenance/contractor/skill/{id_contractor}','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'contractor.skills.change','/maintenance/contractor/skills/change','post',null,null,1);



-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'contractor.locations.get','/maintenance/contractor/location/{id_contractor}','post',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'contractor.locations.change','/maintenance/contractor/locations/change','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'mgt.maintenance.create','/maintenance/mgt/create','get',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'mgt.maintenance.new.save','/maintenance/mgt/new/save','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.start','/maintenance/start/{id_maintenance}','post',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.end','/maintenance/end/{id_maintenance}','post',null,null,1);

-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('mgt.maintenance.start','/maintenance/mgt/start/{id_maintenance}','post',1);


-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('mgt.maintenance.end','/maintenance/mgt/end/{id_maintenance}','post',1);

-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('maintenance.status.chart','/maintenance/statuses/charts','post',1);

-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('mgt.maintenance.status.get_data','/maintenance/mgt/statuses/charts','post',1);

-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('maintenance.sla.chart','/maintenance/sla/charts','post',1);


-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('mgt.maintenance.sla.get_data','/maintenance/mgt/sla/charts','post',1);


-- INSERT INTO permission_route_mapping(permission,route,method,mapping_active)
-- VALUES ('maintenance.contractors.get','/maintenance/contractors_for_assignment','post',1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.get.resident_reporter','/maintenance/get/resident_reporter','post',null,null,1),
--  (null,'maintenance.mgt.resident_reporter','/maintenance/mgt/resident_reporter','post',null,null,1);




-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'contractor.tasks.get','/maintenance/contractor/tasks/{id_contractor}','post',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'contractor.attachments.get','/maintenance/contractor/attachments/{id_contractor}','post',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'contractor.document.delete','/maintenance/contractor_document/delete','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'contractor.attachment.download','/maintenance/contractor_attachment/{id_attachment}/download','get',null,null,1);



-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'contractor.attachment.upload','/maintenance/contractor_file/upload','post',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'contractor.search_skill','/maintenance/contractor_skill/contractors','post',null,null,1);


-- -- contractor management in portall
-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.contractor_management','/maintenance/contractor_management','get',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.mgt_contractors_list','/maintenance/mgt_contractors_list','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.new_contractor_page','/maintenance/new_contractor','get',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.store_contractor','/maintenance/contractor/store','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.contractor_mgt.attachments','/maintenance/mgt_contractor/attachments/{id_contractor}','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.contractor_mgt.tasks','/maintenance/mgt_contractor/tasks/{id_contractor}','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.contractor_mgt.delete','/maintenance/contractor/mgt/delete/{id_contractor}','post',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.contractor_mgt.email_info','/maintenance/mgt_contractor/email/{id_contractor}','post',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.contractor_mgt.change_login_info','/maintenance/mgt_contractor/login_settings/change','post',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.contractor_mgt.location','/maintenance/mgt_contractor/location/{id_contractor}','post',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'mgt.contractor.search_skill','/maintenance/mgt/contractor_skill/contractors','post',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.mgt_contractor.change_location','/maintenance/mgt_contractor/locations/change','post',null,null,1);


-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.mgt_contractor.skill','/maintenance/mgt_contractor/skill/{id_contractor}','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.mgt_contractor.change_skill','/maintenance/mgt_contractor/skills/change','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.mgt_contractor.store','/maintenance/mgt_contractor/portal/store','post',null,null,1);


--preview email content
-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.contractor_email.preview','/maintenance/contractor_email/preview','post',null,null,1);




--send email to contractor
-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.contractor.get.job_document','/maintenance/contractor/job_document/{id_maintenance_job}','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.create.email_temp','/maintenance/create/email_temp/{id_maintenance_job}','get',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.contractor.send.email','/maintenance/contractor/send/email','post',null,null,1);

-- INSERT INTO permission_route_mapping(id_saas_module_list,permission,route,method,created_at,updated_at,mapping_active)
-- VALUES (null,'maintenance.download.email_content','/maintenance/email_content/download','post',null,null,1);











-- Add base data to initialize package
-- INSERT INTO public.maintenance_job_category_ref
-- (job_category_code, job_category_name, job_category_icon, maintenance_job_category_ref_active)
-- VALUES('ELCT', 'Electrical', '-', 1),
-- ('WATR', 'Water', '-', 1),
-- ('GAS', 'Gas', '-', 1),
-- ('STRC', 'Structural', '-', 1),
-- ('MISC', 'Misc', '-', 1);



-- INSERT INTO public.maintenance_job_priority_ref
-- (priority_code, priority_name, priority_icon, maintenance_job_priority_ref_active)
-- VALUES('low', 'Low', '-', 1),
-- ('mdm', 'Medium', '-', 1),
-- ('high', 'High', '-', 1),
-- ('ugnt', 'Urgent', '-', 1);

-- INSERT INTO public.maintenance_job_status_ref
-- (job_status_code, job_status_name, job_status_icon, maintenance_job_status_ref_active)
-- VALUES('OPUN', 'Open Unassigned', '-', 1),
-- ('OPAS', 'Open Assigned', '-', 1),
-- ('HOLD', 'On Hold', '-', 1),
-- ('INPR', 'In Progress', '-', 1),
-- ('CLOS', 'Closed', '-', 1);


-- INSERT INTO public.maintenance_job_sla_ref
-- (id_saas_client_business, id_maintenance_job_priority_ref, id_client, maximum_expected_seen_minutes, expected_target_minutes, maintenance_job_sla_ref_active)VALUES
-- (1, 1, 1, '30', '3000', 1),
-- (1, 1, 2, '30', '3000', 1),
-- (1, 1, 3, '30', '3000', 1),
-- (1, 1, 4, '30', '3000', 1),
-- (1, 1, 5, '30', '3000', 1),
-- (1, 1, 6, '30', '3000', 1),
-- (1, 1, 7, '30', '3000', 1),
-- (1, 1, 8, '30', '3000', 1),
-- (1, 1, 10, '30', '3000', 1),
-- (1, 1, 11, '30', '3000', 1),
-- (1, 1, 12, '30', '3000', 1),
-- (1, 1, 13, '30', '3000', 1),
-- (1, 1, 14, '30', '3000', 1),
-- (1, 1, 15, '30', '3000', 1),
-- (1, 1, 16, '30', '3000', 1),
-- (1, 1, 17, '30', '3000', 1),
-- (1, 1, 18, '30', '3000', 1),
-- (1, 1, 19, '30', '3000', 1),
-- (1, 1, 20, '30', '3000', 1),
-- (1, 1, 21, '30', '3000', 1),
-- (1, 1, 22, '30', '3000', 1),
-- (1, 1, 23, '30', '3000', 1),
-- (1, 1, 24, '30', '3000', 1),
-- (1, 1, 25, '30', '3000', 1),
-- (1, 1, 26, '30', '3000', 1),
-- (1, 1, 27, '30', '3000', 1),
-- (1, 1, 28, '30', '3000', 1),
-- (1, 1, null, '30', '3000', 1),
-- (1, 2, 1, '30', '3000', 1),
-- (1, 2, 2, '30', '3000', 1),
-- (1, 2, 3, '30', '3000', 1),
-- (1, 2, 4, '30', '3000', 1),
-- (1, 2, 5, '30', '3000', 1),
-- (1, 2, 6, '30', '3000', 1),
-- (1, 2, 7, '30', '3000', 1),
-- (1, 2, 8, '30', '3000', 1),
-- (1, 2, 10, '30', '3000', 1),
-- (1, 2, 11, '30', '3000', 1),
-- (1, 2, 12, '30', '3000', 1),
-- (1, 2, 13, '30', '3000', 1),
-- (1, 2, 14, '30', '3000', 1),
-- (1, 2, 15, '30', '3000', 1),
-- (1, 2, 16, '30', '3000', 1),
-- (1, 2, 17, '30', '3000', 1),
-- (1, 2, 18, '30', '3000', 1),
-- (1, 2, 19, '30', '3000', 1),
-- (1, 2, 20, '30', '3000', 1),
-- (1, 2, 21, '30', '3000', 1),
-- (1, 2, 22, '30', '3000', 1),
-- (1, 2, 23, '30', '3000', 1),
-- (1, 2, 24, '30', '3000', 1),
-- (1, 2, 25, '30', '3000', 1),
-- (1, 2, 26, '30', '3000', 1),
-- (1, 2, 27, '30', '3000', 1),
-- (1, 2, 28, '30', '3000', 1),
-- (1, 2, null, '30', '3000', 1),
-- (1, 3, 1, '30', '3000', 1),
-- (1, 3, 2, '30', '3000', 1),
-- (1, 3, 3, '30', '3000', 1),
-- (1, 3, 4, '30', '3000', 1),
-- (1, 3, 5, '30', '3000', 1),
-- (1, 3, 6, '30', '3000', 1),
-- (1, 3, 7, '30', '3000', 1),
-- (1, 3, 8, '30', '3000', 1),
-- (1, 3, 10, '30', '3000', 1),
-- (1, 3, 11, '30', '3000', 1),
-- (1, 3, 12, '30', '3000', 1),
-- (1, 3, 13, '30', '3000', 1),
-- (1, 3, 14, '30', '3000', 1),
-- (1, 3, 15, '30', '3000', 1),
-- (1, 3, 16, '30', '3000', 1),
-- (1, 3, 17, '30', '3000', 1),
-- (1, 3, 18, '30', '3000', 1),
-- (1, 3, 19, '30', '3000', 1),
-- (1, 3, 20, '30', '3000', 1),
-- (1, 3, 21, '30', '3000', 1),
-- (1, 3, 22, '30', '3000', 1),
-- (1, 3, 23, '30', '3000', 1),
-- (1, 3, 24, '30', '3000', 1),
-- (1, 3, 25, '30', '3000', 1),
-- (1, 3, 26, '30', '3000', 1),
-- (1, 3, 27, '30', '3000', 1),
-- (1, 3, 28, '30', '3000', 1),
-- (1, 3, null, '30', '3000', 1),
-- (1, 4, 1, '30', '3000', 1),
-- (1, 4, 1, '30', '3000', 1),
-- (1, 4, 2, '30', '3000', 1),
-- (1, 4, 3, '30', '3000', 1),
-- (1, 4, 4, '30', '3000', 1),
-- (1, 4, 5, '30', '3000', 1),
-- (1, 4, 6, '30', '3000', 1),
-- (1, 4, 7, '30', '3000', 1),
-- (1, 4, 8, '30', '3000', 1),
-- (1, 4, 10, '30', '3000', 1),
-- (1, 4, 11, '30', '3000', 1),
-- (1, 4, 12, '30', '3000', 1),
-- (1, 4, 13, '30', '3000', 1),
-- (1, 4, 14, '30', '3000', 1),
-- (1, 4, 15, '30', '3000', 1),
-- (1, 4, 16, '30', '3000', 1),
-- (1, 4, 17, '30', '3000', 1),
-- (1, 4, 18, '30', '3000', 1),
-- (1, 4, 19, '30', '3000', 1),
-- (1, 4, 20, '30', '3000', 1),
-- (1, 4, 21, '30', '3000', 1),
-- (1, 4, 22, '30', '3000', 1),
-- (1, 4, 23, '30', '3000', 1),
-- (1, 4, 24, '30', '3000', 1),
-- (1, 4, 25, '30', '3000', 1),
-- (1, 4, 26, '30', '3000', 1),
-- (1, 4, 27, '30', '3000', 1),
-- (1, 4, 28, '30', '3000', 1),
-- (1, 4, null, '30', '3000', 1);

-- INSERT INTO public.maintenance_job_sla_ref
-- (id_saas_client_business, id_maintenance_job_priority_ref, id_client, maximum_expected_seen_minutes, expected_target_minutes, maintenance_job_sla_ref_active)VALUES
-- (1, 1, 28, '30', '3000', 1),
-- (1, 2, 28, '30', '3000', 1),
-- (1, 3, 28, '30', '3000', 1),
-- (1, 4, 28, '30', '3000', 1),
-- (1, 1, 29, '30', '3000', 1),
-- (1, 2, 29, '30', '3000', 1),
-- (1, 3, 29, '30', '3000', 1),
-- (1, 4, 29, '30', '3000', 1),
-- (1, 1, 30, '30', '3000', 1),
-- (1, 2, 30, '30', '3000', 1),
-- (1, 3, 30, '30', '3000', 1),
-- (1, 4, 30, '30', '3000', 1),
-- (1, 1, 31, '30', '3000', 1),
-- (1, 2, 31, '30', '3000', 1),
-- (1, 3, 31, '30', '3000', 1),
-- (1, 4, 31, '30', '3000', 1);


-- INSERT INTO public.contractor_location_ref (location,contractor_location_ref_active) VALUES
--	('Woolston',1),
--	('Eastleigh',1),
--	('West End',1),
--	('Hailsham',1);
-- ('Hampshire', 1),
-- ('St Leonards On Sea', 1),
-- ('Eastbourne', 1),
-- ('Sussex', 1),
-- ('Shoreham', 1),
-- ('Worthing', 1);






-- -- comment this
-- INSERT INTO public.contractor_skill_ref
-- (skill_name, contractor_skill_ref_active)VALUES
-- ('Handyman Services', 1),
-- ('Plumbing Services', 1),
-- ('Rescue Services', 1),
-- ('Electrical Services', 1),
-- ('Security Services', 1),
-- ('Cleaning Services', 1),
-- ('Garden Services', 1),
-- ('Fire Protection Services', 1),
-- ('Domestics Services', 1),
-- ('Repairs Services', 1),
-- ('Synergy Services', 1),
-- ('Furniture Restoration Services', 1),
-- ('Lift Maintenance Services', 1),
-- ('Doors & Windows Services', 1),
-- ('Appliance Repair Services', 1),
-- ('Carpet Cleaning Services', 1),
-- ('Drainage Services', 1);




-- insert into system_variable_category_ref(id_system_variable_category_ref , variable_category_name , friendly_name , category_visible , system_variable_category_ref_active) values(8 , 'maintenance' ,'MAINTENANCE',1,1);



-- insert into system_variable_ref(id_system_variable_category_ref , variable_name , friendly_name , variable_code , variable_description , code_visible , system_variable_ref_active) values
-- (8 , 'MAINTENANCE_TITLE' ,'MAINTENANCE_TITLE' , '%%MAINTENANCE_TITLE%%' ,'',1,1),
-- (8 , 'MAINTENANCE_DETAIL' ,'MAINTENANCE_DETAIL' , '%%MAINTENANCE_DETAIL%%' ,'',1,1),
-- (8 , 'MAINTENANCE_CATEGORY' ,'MAINTENANCE_CATEGORY' , '%%MAINTENANCE_CATEGORY%%' ,'',1,1),
-- (8 , 'MAINTENANCE_LOCATION' ,'MAINTENANCE_LOCATION' , '%%MAINTENANCE_LOCATION%%' ,'',1,1),
-- (8 , 'MAINTENANCE_PRIORITY' ,'MAINTENANCE_PRIORITY' , '%%MAINTENANCE_PRIORITY%%' ,'',1,1),
-- (8 , 'MAINTENANCE_STATUS' ,'MAINTENANCE_STATUS' , '%%MAINTENANCE_STATUS%%' ,'',1,1),
-- (8 , 'RESIDENT_REPORTER' ,'RESIDENT_REPORTER' , '%%RESIDENT_REPORTER%%' ,'',1,1),
-- (8 , 'JOB_START_DATE_TIME' ,'JOB_START_DATE_TIME' , '%%JOB_START_DATE_TIME%%' ,'',1,1),
-- (8 , 'JOB_FINISH_DATE_TIME' ,'JOB_FINISH_DATE_TIME' , '%%JOB_FINISH_DATE_TIME%%' ,'',1,1),
-- (8 , 'LEGAL_COMPANY_LOGO' ,'LEGAL_COMPANY_LOGO' , '%%LEGAL_COMPANY_LOGO%%' ,'',1,1),
-- (8 , 'LEGAL_COMPANY_NAME' ,'LEGAL_COMPANY_NAME' , '%%LEGAL_COMPANY_NAME%%' ,'',1,1),
-- (8 , 'LEGAL_COMPANY_SHORT_NAME' ,'LEGAL_COMPANY_SHORT_NAME' , '%%LEGAL_COMPANY_SHORT_NAME%%' ,'',1,1),
-- (8 , 'ORDER_NUMBER' ,'ORDER_NUMBER' , '%%ORDER_NUMBER%%' ,'',1,1),
-- (8 , 'CONTRACTOR_NAME' ,'CONTRACTOR_NAME' , '%%CONTRACTOR_NAME%%' ,'',1,1),
-- (8 , 'CONTRACTOR_SHORT_NAME' ,'CONTRACTOR_SHORT_NAME' , '%%CONTRACTOR_SHORT_NAME%%' ,'',1,1),
-- (8 , 'CONTRACTOR_VAT_NUMBER' ,'CONTRACTOR_VAT_NUMBER' , '%%CONTRACTOR_VAT_NUMBER%%' ,'',1,1),
-- (8 , 'CONTRACTOR_TEL_NUMBER1' ,'CONTRACTOR_TEL_NUMBER1' , '%%CONTRACTOR_TEL_NUMBER1%%' ,'',1,1),
-- (8 , 'CONTRACTOR_TEL_NUMBER2' ,'CONTRACTOR_TEL_NUMBER2' , '%%CONTRACTOR_TEL_NUMBER2%%' ,'',1,1),
-- (8 , 'CONTRACTOR_ADDRESS' ,'CONTRACTOR_ADDRESS' , '%%CONTRACTOR_ADDRESS%%' ,'',1,1),
-- (8 , 'COMMENCEMENT_DATE' ,'COMMENCEMENT_DATE' , '%%COMMENCEMENT_DATE%%' ,'',1,1),
-- (8 , 'COMPLETE_DATE' ,'COMPLETE_DATE' , '%%COMPLETE_DATE%%' ,'',1,1),
-- (8 , 'CONTRACTOR_NOTE' ,'CONTRACTOR_NOTE' , '%%CONTRACTOR_NOTE%%' ,'',1,1);






-- data related to maintenance template
-- insert into template_category(id_template_category,template_category_name , description , is_visible_in_booking , template_category_active)
-- values(9,'maintenance' ,'All templates relating to maintenances',0,1);


-- insert into template_sub_category(id_template_sub_category,id_template_category , template_sub_category_name , description , is_visible_in_booking,template_sub_category_active)
-- values(3,9,'maintenance sent to contractor' ,'maintenance sent to contractor',0,1);





-- update template set template_message_body = '<h3><b>Work Instruction</b></h3><p><b>Works issued on behalf of %%LEGAL_COMPANY_NAME%%</b></p><p>%%LEGAL_COMPANY_LOGO%%</p><br><br><br><p>Order Number: %%ORDER_NUMBER%%</p><p>Date: %%DATE%%</p>
-- <p>Contact: Emma Weston</p><p>Direct Dial: 07741300764</p><p>Email:  emma.weston@sdrgroup.co.uk</p><br><div style="margin:10px auto;text-align: center;border-width:3px; border-style:solid; border-color: #0f0f0f ;"><span><b>Please address invoice to:</b></span><br> <span>In Chorus Ltd, 48 Malling Street, Lewes, BN7 2RH </span><br><span><b>Quote order no:</b> xxxx and email to: emma.weston@sdrgroup.co.uk  </span></div><br><br><p>Site Address:</p><p>Commencement: %%COMMENCEMENT_DATE%%</p><p>Oaklea</p><p>29 Oak Road</p><p>Complete By: %%COMPLETE_DATE%%</p><p>Woolston</p><p>Southampton</p><p>SO19 9BQ</p><br><br><p><b>%%MAINTENANCE_TITLE%%</b></p><p><b>%%MAINTENANCE_DETAIL%%</b></p><p>Please can you attend the above-mentioned site and carry out and EICR. Attached for reference is the previous report. </p><p>Known Hazards and Health and Safety: </p><p>The contractor should ensure that all works relating to this works order are carried out in accordance with Health and Safety Policies and, where applicable the provisions of the Construction and Design Management Regulations 2015. Asbestos surveys are kept onsite, and a digital copy can be sent upon request. Should you have any concerns regarding this instruction, please raise them with us before commencing works.</p><br><br><br>'
-- where id_template_category =



insert into system_variable_ref(id_system_variable_category_ref , variable_name , friendly_name , variable_code , variable_description , code_visible , system_variable_ref_active) values
(8,'MAINTENANCE_SITE','MAINTENANCE_SITE','%%MAINTENANCE_SITE%%',NULL,1,1);



update template  set template_message_body ='<h3><strong>Work Instruction</strong></h3>
<p><strong>Works issued on behalf of %%LEGAL_COMPANY_NAME%%</strong></p></br>
<p style="text-align:right;margin-right:5px">%%LEGAL_COMPANY_LOGO%%</p>
<p><br /><br /><br /></p>
<p>Order Number: %%ORDER_NUMBER%%</p>
<p>Date: %%DATE%%</p>
<p>Contact: Emma Weston</p>
<p>Direct Dial: 07741300764</p>
<p>Email: emma.weston@sdrgroup.co.uk</p>
<p>&nbsp;</p>
<p><br /><br /></p>

<div style="margin: 10px auto; color: black; text-align: center; border: 3px solid #0f0f0f;"><p style="font-weight: bold;">Please address invoice to:</p>In Chorus Ltd, 48 Malling Street, Lewes, BN7 2RH <br /><strong style="font-weight: bold;">Quote order no: %%ORDER_NUMBER%% </strong> and email to: emma.weston@sdrgroup.co.uk</div>
<p><br /><br /></p>
<div style="width:100%;height:100px;">
<div style="float:left;display:inline-block;width:40%;height:100px;">
<p style="margin:0;display:inline;text-align:left;font-weight: bold">Site Address:</p>
<p>%%MAINTENANCE_SITE%%</p>


</div>
<div style="float:right;display:inline-block;width:40%;height:100px">
<p style="margin:0;display:inline;text-align:right">Commencement: %%COMMENCEMENT_DATE%%</p>
<p><br /><br /></p>

<p style="margin:0;display:inline;text-align:right">Complete By: %%COMPLETE_DATE%%</p>
</div>
</div>
<p></p>
<p><br /><br /></p>
<p style="font-weight: bold;text-decoration: underline;"><strong> Job Title( %%MAINTENANCE_TITLE%% )</strong></p>
<p style="font-weight: bold"><strong>%%MAINTENANCE_DETAIL%%</strong></p>
<p>Known Hazards and Health and Safety:</p>
<p>The contractor should ensure that all works relating to this works order are carried out in accordance with Health and Safety Policies and, where applicable the provisions of the Construction and Design Management Regulations 2015. Asbestos surveys are kept onsite, and a digital copy can be sent upon request. Should you have any concerns regarding this instruction, please raise them with us before commencing works.</p>
'
where id_template_category = 9;




