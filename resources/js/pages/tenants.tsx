import { Head } from '@inertiajs/react';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { tenants as tenantsRoute } from '@/routes';

interface Floor { name: string }
interface Room { name: string; floor: Floor }
interface Bed { name: string; room: Room }
interface Booking { id: number; check_in_date: string; status: string; bed: Bed }

interface Tenant {
    id: number;
    first_name: string;
    middle_name: string | null;
    last_name: string;
    full_name: string;
    gender: 'male' | 'female' | 'other';
    date_of_birth: string | null;
    blood_group: string | null;
    phone: string;
    secondary_phone: string | null;
    email: string | null;
    permanent_address: string | null;
    current_address: string | null;
    father_name: string | null;
    father_phone: string | null;
    mother_name: string | null;
    local_guardian_name: string | null;
    local_guardian_phone: string | null;
    local_guardian_relationship: string | null;
    citizenship_number: string | null;
    citizenship_issued_district: string | null;
    occupation_status: 'student' | 'job_holder' | null;
    organization_name: string | null;
    level_designation: string | null;
    emergency_contact_name: string | null;
    emergency_contact_phone: string | null;
    joined_date: string | null;
    monthly_rent_agreed: string | null;
    meal_preference: string | null;
    status: string;
    active_booking?: {
        current_bed_assignment?: {
            bed: Bed
        }
    };
}

export default function Tenants({ tenants }: { tenants: Tenant[] }) {
    return (
        <>
            <Head title="Tenants" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">Tenants</h1>
                    <p className="text-sm text-muted-foreground">Manage your residents, their details, and history.</p>
                </div>

                <Card>
                    <CardContent className="p-0">
                        <div className="overflow-x-auto rounded-md border">
                            <table className="w-full text-sm text-left whitespace-nowrap">
                                <thead className="bg-muted/50 border-b">
                                    <tr>
                                        <th className="px-4 py-3 font-medium">Name</th>
                                        <th className="px-4 py-3 font-medium">Status</th>
                                        <th className="px-4 py-3 font-medium">Bed</th>
                                        <th className="px-4 py-3 font-medium">Contact</th>
                                        <th className="px-4 py-3 font-medium text-right">Rent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {tenants.map((tenant) => {
                                        return (
                                            <tr key={tenant.id} className="border-b last:border-0 hover:bg-muted/30">
                                                <td className="px-4 py-3 font-medium">{tenant.full_name}</td>
                                                <td className="px-4 py-3">
                                                    <Badge variant={tenant.status === 'active' ? 'default' : 'secondary'}>
                                                        {tenant.status}
                                                    </Badge>
                                                </td>
                                                <td className="px-4 py-3">
                                                    {tenant.active_booking?.current_bed_assignment ? (
                                                        <div className="flex flex-col">
                                                            <span className="font-medium">Bed {tenant.active_booking.current_bed_assignment.bed.name}</span>
                                                            <span className="text-xs text-muted-foreground">{tenant.active_booking.current_bed_assignment.bed.room.name}, {tenant.active_booking.current_bed_assignment.bed.room.floor.name}</span>
                                                        </div>
                                                    ) : (
                                                        <span className="text-muted-foreground italic">Unassigned</span>
                                                    )}
                                                </td>
                                                <td className="px-4 py-3">
                                                    <div className="flex flex-col">
                                                        <span>{tenant.phone}</span>
                                                        {tenant.email && (
                                                            <span className="text-xs text-muted-foreground">{tenant.email}</span>
                                                        )}
                                                    </div>
                                                </td>
                                                <td className="px-4 py-3 font-medium text-right">
                                                    {tenant.monthly_rent_agreed ? `रु ${Number(tenant.monthly_rent_agreed).toLocaleString()}` : '-'}
                                                </td>
                                            </tr>
                                        );
                                    })}
                                    
                                    {tenants.length === 0 && (
                                        <tr>
                                            <td colSpan={5} className="px-4 py-8 text-center text-muted-foreground">
                                                No tenants found.
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

Tenants.layout = {
    breadcrumbs: [
        {
            title: 'Tenants',
            href: tenantsRoute(),
        },
    ],
};
