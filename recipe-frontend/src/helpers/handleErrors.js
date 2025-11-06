export function extractServerMessage(resp) {
    const msg = resp?.data?.message
    if (resp?.status === 422) {
        const errs = resp?.data?.errors || {}
        const firstFieldMsg = Object.values(errs)?.[0]?.[0]
        return firstFieldMsg || msg || 'Validation failed.'
    }
    return msg || 'Something went wrong.'
}