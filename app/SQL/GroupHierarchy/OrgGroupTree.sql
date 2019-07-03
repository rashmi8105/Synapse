#Migration script for creating group closure table and loading data into it from org_group.

DROP TABLE IF EXISTS org_group_tree;
CREATE TABLE org_group_tree
(
  id                  INT AUTO_INCREMENT NOT NULL,
  ancestor_group_id   INT                NOT NULL,
  descendant_group_id INT                NOT NULL,
  path_length         SMALLINT DEFAULT 0 NOT NULL,
  created_by          INT      DEFAULT NULL,
  created_at          DATETIME DEFAULT NULL,
  modified_by         INT      DEFAULT NULL,
  modified_at         DATETIME DEFAULT NULL,
  deleted_by          INT      DEFAULT NULL,
  deleted_at          DATETIME DEFAULT NULL,
  INDEX FK_ancestor_group_id_IDX (ancestor_group_id),
  INDEX FK_descendant_group_id_IDX (descendant_group_id),
  INDEX IDX_ancestor_descendant (ancestor_group_id, descendant_group_id, deleted_at),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;

ALTER TABLE org_group_tree ADD CONSTRAINT FK_1B384A68DE12AB56 FOREIGN KEY (created_by) REFERENCES person (id);
ALTER TABLE org_group_tree ADD CONSTRAINT FK_1B384A6825F94802 FOREIGN KEY (modified_by) REFERENCES person (id);
ALTER TABLE org_group_tree ADD CONSTRAINT FK_1B384A681F6FA0AF FOREIGN KEY (deleted_by) REFERENCES person (id);
ALTER TABLE org_group_tree ADD CONSTRAINT FK_1B384A68582DCD11 FOREIGN KEY (ancestor_group_id) REFERENCES org_group (id);
ALTER TABLE org_group_tree ADD CONSTRAINT FK_1B384A68FCA5FABF FOREIGN KEY (descendant_group_id) REFERENCES org_group (id);


INSERT INTO org_group_tree(ancestor_group_id, descendant_group_id, path_length, created_at, modified_at, deleted_at) SELECT id, id, 0, created_at, modified_at, deleted_at FROM org_group ;
INSERT INTO org_group_tree(ancestor_group_id, descendant_group_id, path_length, created_at, modified_at, deleted_at) SELECT og2.id, og1.id, 1, og1.created_at, og1.modified_at, coalesce(og1.deleted_at, og2.deleted_at) FROM org_group og1 JOIN org_group og2 ON og1.parent_group_id = og2.id WHERE og2.id IS NOT NULL	;
INSERT INTO org_group_tree(ancestor_group_id, descendant_group_id, path_length, created_at, modified_at, deleted_at) SELECT og3.id, og1.id, 2, og1.created_at, og1.modified_at, coalesce(og1.deleted_at, og2.deleted_at, og3.deleted_at) FROM org_group og1 JOIN org_group og2 ON og1.parent_group_id = og2.id JOIN org_group og3 ON og2.parent_group_id = og3.id WHERE og3.id IS NOT NULL;
INSERT INTO org_group_tree(ancestor_group_id, descendant_group_id, path_length, created_at, modified_at, deleted_at) SELECT og4.id, og1.id, 3, og1.created_at, og1.modified_at, coalesce(og1.deleted_at, og2.deleted_at, og3.deleted_at, og4.deleted_at) FROM org_group og1 JOIN org_group og2 ON og1.parent_group_id = og2.id JOIN org_group og3 ON og2.parent_group_id = og3.id JOIN org_group og4 ON og3.parent_group_id = og4.id WHERE og4.id IS NOT NULL;
INSERT INTO org_group_tree(ancestor_group_id, descendant_group_id, path_length, created_at, modified_at, deleted_at) SELECT og5.id, og1.id, 4, og1.created_at, og1.modified_at, coalesce(og1.deleted_at, og2.deleted_at, og3.deleted_at, og4.deleted_at, og5.deleted_at) FROM org_group og1 JOIN org_group og2 ON og1.parent_group_id = og2.id JOIN org_group og3 ON og2.parent_group_id = og3.id JOIN org_group og4 ON og3.parent_group_id = og4.id JOIN org_group og5 ON og4.parent_group_id = og5.id WHERE og5.id IS NOT NULL;
INSERT INTO org_group_tree(ancestor_group_id, descendant_group_id, path_length, created_at, modified_at, deleted_at) SELECT og6.id, og1.id, 5, og1.created_at, og1.modified_at, coalesce(og1.deleted_at, og2.deleted_at, og3.deleted_at, og4.deleted_at, og5.deleted_at, og6.deleted_at) FROM org_group og1 JOIN org_group og2 ON og1.parent_group_id = og2.id JOIN org_group og3 ON og2.parent_group_id = og3.id JOIN org_group og4 ON og3.parent_group_id = og4.id JOIN org_group og5 ON og4.parent_group_id = og5.id JOIN org_group og6 ON og5.parent_group_id = og6.id WHERE og6.id IS NOT NULL;
INSERT INTO org_group_tree(ancestor_group_id, descendant_group_id, path_length, created_at, modified_at, deleted_at) SELECT og7.id, og1.id, 6, og1.created_at, og1.modified_at, coalesce(og1.deleted_at, og2.deleted_at, og3.deleted_at, og4.deleted_at, og5.deleted_at, og6.deleted_at, og7.deleted_at) FROM org_group og1 JOIN org_group og2 ON og1.parent_group_id = og2.id JOIN org_group og3 ON og2.parent_group_id = og3.id JOIN org_group og4 ON og3.parent_group_id = og4.id JOIN org_group og5 ON og4.parent_group_id = og5.id JOIN org_group og6 ON og5.parent_group_id = og6.id JOIN org_group og7 ON og6.parent_group_id = og7.id WHERE og7.id IS NOT NULL;