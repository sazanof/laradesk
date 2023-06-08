export function statusClass(number) {
    switch (number) {
        case 1:
            return 'new'
        case 2:
            return 'in_work'
        case 3:
            return 'waiting'
        case 4:
            return 'solved'
        case 5:
            return 'closed'
        case 6:
            return 'in_approval'
    }
}
