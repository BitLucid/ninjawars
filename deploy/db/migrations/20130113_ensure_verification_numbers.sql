update accounts set verification_number = trunc(random() * 9999 + 1) where verification_number is null;
