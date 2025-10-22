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
        case 7:
            return 'approved'
    }
}

export function statusColor(number) {
    switch (number) {
        case 1:
            return 'info'
        case 2:
            return 'purple'
        case 3:
            return 'yellow'
        case 4:
            return 'success'
        case 5:
            return 'error'
        case 6:
            return 'grey'
        case 7:
            return 'cyan'
    }
}
