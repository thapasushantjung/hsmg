import { Head } from '@inertiajs/react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { DollarSign, ArrowUpRight, ArrowDownRight, Clock } from 'lucide-react';
import { finance as financeRoute } from '@/routes';

interface Tenant { full_name: string }
interface Room { name: string }
interface Bed { name: string; room: Room }
interface Booking { current_bed_assignment?: { bed: Bed } }

interface Payment {
    id: number;
    amount: string;
    type: string;
    due_date: string;
    due_date_bs: string | null;
    paid_date: string | null;
    paid_date_bs: string | null;
    status: 'pending' | 'paid' | 'overdue' | 'cancelled';
    tenant: Tenant;
    booking: Booking;
}

interface Stats {
    total_revenue: string;
    pending_dues: string;
    overdue_dues: string;
}

export default function Finance({ payments, stats }: { payments: Payment[]; stats: Stats }) {
    const formatCurrency = (amount: string) => {
        return `रु ${parseFloat(amount).toLocaleString('en-NP')}`;
    };

    return (
        <>
            <Head title="Finance" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">Finance Overview</h1>
                    <p className="text-sm text-muted-foreground">Track revenue, pending payments, and tenant dues.</p>
                </div>

                <div className="grid gap-4 md:grid-cols-3">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Total Revenue</CardTitle>
                            <DollarSign className="h-4 w-4 text-emerald-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.total_revenue || '0')}</div>
                            <p className="text-xs text-muted-foreground mt-1 flex items-center">
                                <ArrowUpRight className="mr-1 h-3 w-3 text-emerald-500" />
                                All time collected
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium">Pending Dues</CardTitle>
                            <Clock className="h-4 w-4 text-amber-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold">{formatCurrency(stats.pending_dues || '0')}</div>
                            <p className="text-xs text-muted-foreground mt-1 flex items-center">
                                Upcoming payments
                            </p>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                            <CardTitle className="text-sm font-medium text-rose-500">Overdue Dues</CardTitle>
                            <ArrowDownRight className="h-4 w-4 text-rose-500" />
                        </CardHeader>
                        <CardContent>
                            <div className="text-2xl font-bold text-rose-500">{formatCurrency(stats.overdue_dues || '0')}</div>
                            <p className="text-xs text-muted-foreground mt-1">
                                Requires immediate attention
                            </p>
                        </CardContent>
                    </Card>
                </div>

                <Card className="mt-4">
                    <CardHeader>
                        <CardTitle>Recent Transactions</CardTitle>
                    </CardHeader>
                    <CardContent className="p-0">
                        <div className="rounded-md border-t border-x-0 border-b-0 md:border-x md:border-b md:border-t-0">
                            <table className="w-full text-sm text-left">
                                <thead className="bg-muted/50 border-b">
                                    <tr>
                                        <th className="px-4 py-3 font-medium">Tenant</th>
                                        <th className="px-4 py-3 font-medium">Bed/Room</th>
                                        <th className="px-4 py-3 font-medium">Type</th>
                                        <th className="px-4 py-3 font-medium">Amount</th>
                                        <th className="px-4 py-3 font-medium">Due Date</th>
                                        <th className="px-4 py-3 font-medium text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {payments.map((payment) => (
                                        <tr key={payment.id} className="border-b last:border-0 hover:bg-muted/30">
                                            <td className="px-4 py-3 font-medium">{payment.tenant.full_name}</td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {payment.booking?.current_bed_assignment ? `Bed ${payment.booking.current_bed_assignment.bed.name}, Room ${payment.booking.current_bed_assignment.bed.room.name}` : '-'}
                                            </td>
                                            <td className="px-4 py-3 capitalize">{payment.type}</td>
                                            <td className="px-4 py-3 font-medium">{formatCurrency(payment.amount)}</td>
                                            <td className="px-4 py-3 text-muted-foreground">{payment.due_date_bs}</td>
                                            <td className="px-4 py-3 text-right">
                                                <Badge 
                                                    variant={
                                                        payment.status === 'paid' ? 'default' : 
                                                        payment.status === 'overdue' ? 'destructive' : 'secondary'
                                                    }
                                                >
                                                    {payment.status}
                                                </Badge>
                                            </td>
                                        </tr>
                                    ))}
                                    
                                    {payments.length === 0 && (
                                        <tr>
                                            <td colSpan={6} className="px-4 py-8 text-center text-muted-foreground">
                                                No transactions found.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

Finance.layout = {
    breadcrumbs: [
        {
            title: 'Finance',
            href: financeRoute(),
        },
    ],
};
