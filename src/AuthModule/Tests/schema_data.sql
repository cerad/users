use tests;

INSERT INTO users
(id,user_name,disp_name,email,password,salt,roles,person_key)
VALUES
( 1,
  'ahundiak','Art Hundiak','ahundiak@testing.com',
  'tfNORVo3b6P0EpBzApQpxP8/B2xM/LnCnL1AbtWGDV6bmDAAPY5cpWcdF/E+RcEUXixDZM9s6lZL8LPFTN3rYw==',
  'salt',
  'ROLE_USER,ROLE_SUPER_ADMIN',
  'C4AF1DBD-4945-4269-97A6-E2E203319D58'
),
( 2,
  'bailey5000','Bill Bailey','bailey5000@testing.com',
  'tyo48VJsCv9YW3/hw2HrPgJ9RIdNcLBMps1v0ayOwVDgzM1jGhUFi2SdhSbS1evPqWd+5nF64VBzwZXDC8tDOg==',
  'salt',
  'ROLE_USER',
  '1F9BB8B8-0D8F-414D-9763-E4679E882D67'
),
( 3,
  'ayso1sra@testing.com','Rick Roberts','ayso1sra@testing.com',
  'Fn+9aBGM9L04FO4YrDZMuKvgIn8ZC6dHBkpAnSQv5yGEiqs94S1uZZCokuqSjBbkwMs6gn0oxWbFnL2loEzgEw==',
  'salt',
  'ROLE_USER,ROLE_ADMIN',
  '7A43DF09-7D0F-4CA2-B991-305094B2340E'
);
