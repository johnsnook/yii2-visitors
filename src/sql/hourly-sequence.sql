/*
 * @author John Snook
 * @date Aug 30, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * Description of hourly-sequence
 */
/**
 * Author:  snook
 * Created: Aug 30, 2018
 */

with hours as (
select to_char(hour, 'YYYY-MM-DD HH24') as hour
from generate_series(
(select min(created_at) from visitor),
(select max(created_at) from visitor),
'1 hour'
) hour
)
select (h.hour || ':00')::timestamp as hour, count(distinct vs.ip)
from hours h left join visitor vs on to_char(vs.created_at, 'YYYY-MM-DD HH24') = h.hour
group by h.hour
order by h.hour ;


select (to_char(created_at, 'YYYY-MM-DD HH24') || ':00')::timestamp as time, count(distinct ip) as visitors
group by created_at
order created_at;


SELECT
  distinct (
    to_char(created_at, 'YYYY-MM-DD HH24') || ':00'
  )::timestamp AS "x",
  count(*) AS "visitors"
FROM
  "visitor"
GROUP BY
  "x"
ORDER BY
  "x";

SELECT distinct x, count(s.*) AS "visits"
from visits v,(
    SELECT distinct
        (to_char(created_at, 'YYYY-MM-DD HH24') || ':00')::timestamp AS "x"
    FROM
      "visits"
    GROUP BY
      "x"
    ORDER BY
      "x"
) as s
WHERE (to_char(created_at, 'YYYY-MM-DD HH24') || ':00')::timestamp = s.x
    GROUP BY
      "x"
    ORDER BY
      "x";
