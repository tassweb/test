select distinct B.followup_by, A.donor_id, A.last_name, A.first_name, A.company_name, A.home_phone, A.work_phone, A.email_address, B.contact_text, B.contact_by 
from dimdonortb A inner join fctcontacttb B on (A.donor_id = B.donor_id and B.contact_date between A.from_date and A.to_date)
where B.followup_ind = 'Y'
order by 1,3,4,5