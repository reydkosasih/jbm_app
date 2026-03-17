# Test Scenarios

## Scenario 1: Customer Full Journey
1. Register a new customer account.
2. Add at least one vehicle.
3. Create a booking through all wizard steps.
4. Login as admin and confirm the booking.
5. Update status to `in_progress`, then generate invoice.
6. Open customer status page and verify polling updates the badge/timeline.
7. Choose transfer payment and upload proof.
8. Confirm the payment as admin.
9. Open and print the generated invoice.

## Scenario 2: Overbooking Prevention
1. Fill a slot to its maximum booking capacity on one date.
2. Attempt another booking on the same slot/date.
3. Verify the API marks the slot full.
4. Verify booking submit returns a validation error.

## Scenario 3: Stock Depletion
1. Set one spare part stock to `1`.
2. Add the part into a service order.
3. Verify stock decreases to `0`.
4. Verify low-stock indicators appear in admin dashboard/stock view.

## Scenario 4: Access Control
1. Visit `/admin` without login and verify redirect to `/login`.
2. Login as customer and visit `/admin`; verify access denied.
3. Login as non-admin/non-kasir role and verify protected admin routes fail.

## Scenario 5: CSRF Protection
1. Submit an AJAX POST without CSRF body token.
2. Submit an AJAX POST with stale token.
3. Verify request is rejected.
4. Submit with current token and verify success.

## Scenario 6: Upload Validation
1. Upload a valid JPG proof under 2MB.
2. Upload an invalid executable or oversized file.
3. Verify only the valid file is accepted.
