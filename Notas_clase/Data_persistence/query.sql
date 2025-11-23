SELECT M.id,
       M.nombre AS materia,
       M.descripcion,
       CONCAT(P.nombres, " ", P.a_paterno, " ", P.a_materno) AS Profesor,
       E.estado,
       M.fecha_creacion,
       M.fecha_modificacion
FROM materias AS M
INNER JOIN usuarios as P
ON M.id_profesor = P.id
INNER JOIN estatus AS E
ON M.id_estatus = E.id;