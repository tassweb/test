select distinct B.type_name, A.last_name, A.first_name, A.address, A.address_2, A.city, A.postal_code, A.email_address, A.contact_type, 
B.donation_date, C.fund_name, D.origin_name, B.donation_notes, sum(B.donation_amt) as "donation_amt"
from dimdonortb A
inner join fctdonationstb B on (A.donor_id = B.donor_id and current_date between A.from_date and A.to_date)
left outer join dimfundtb C on (B.fund_id = C.fund_id and current_date between C.from_date and C.to_date)
left outer join dimorigintb D on (B.origin_id = D.origin_id and current_date between D.from_date and D.to_date)
where 1=1
and B.donation_date >= @FROM_DATE@
and B.donation_date <= @TO_DATE@
group by 1,2,3,4,5,6,7,8,9,10,11,12,13
order by 1,2,3
