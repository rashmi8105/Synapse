CREATE OR REPLACE VIEW person_search
AS
  SELECT
    id as person_id,
    external_id,
    firstname,
    lastname,
    organization_id,
    concat(firstname, lastname) as first_and_last_name,
    concat(lastname, firstname) as last_and_first_name,
    username
  FROM synapse.person
  WHERE deleted_at IS NULL;