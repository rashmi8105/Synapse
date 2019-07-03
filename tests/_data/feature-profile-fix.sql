/** profile fix **/
UPDATE `synapse`.`ebi_search` SET `query`='select mm.id,mm.metadata_type,dml.datablock_desc as blockdesc,mml.meta_name,pm.metadata_value as myanswer from datablock_master dm join datablock_master_lang dml ON dm.id = dml.datablock_id JOIN  datablock_metadata dmd ON dmd.datablock_id = dm.id JOIN ebi_metadata mm ON dmd.ebi_metadata_id = mm.id JOIN ebi_metadata_lang mml ON mml.ebi_metadata_id = mm.id JOIN person_ebi_metadata pm ON pm.ebi_metadata_id = mm.id  where mml.lang_id=$$lang$$ AND dm.block_type="profile" AND pm.person_id = $$studentid$$ AND dm.id IN($$datablockpermission$$) AND mm.deleted_at IS NULL AND dm.deleted_at IS NULL' WHERE `query_key`='Student_Profile_Datablock_Info';

UPDATE `synapse`.`ebi_search` SET `query`='select mm.id,mm.metadata_type,mm.meta_name,pm.metadata_value as myanswer from  org_metadata mm JOIN person_org_metadata pm ON pm.org_metadata_id = mm.id  where mm.definition_type="O" AND pm.person_id = $$studentid$$ AND mm.deleted_at IS NULL' WHERE `query_key`='Student_Profile_ISP_Info';