--ACCESS=
SELECT n.title, fv.field_valor_value, fe.field_executou_value
FROM node n

INNER JOIN field_data_field_valor fv
ON fv.entity_id = n.nid

INNER JOIN field_data_field_executou fe
ON fe.entity_id = n.nid