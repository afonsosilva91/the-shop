import React from 'react';
import axios from 'axios'

import Grid from '@material-ui/core/Grid';
import Paper from '@material-ui/core/Paper';

import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';


class OrdersList extends React.Component {

    componentWillMount() {
        axios.get('http://localhost:8081')
            .then(response => console.log(response))
    }

    render() {
        return (
            <div className="order-list">
                <Grid container direction="row" justify="center" alignItems="center" xs={6}>
                    <Paper>
                        <Table>
                            <TableHead>
                                <TableRow>
                                    <TableCell>#ID</TableCell>
                                    <TableCell>Customer</TableCell>
                                    <TableCell>Order</TableCell>
                                    <TableCell>Discount</TableCell>
                                    <TableCell>Total</TableCell>
                                    <TableCell>Date</TableCell>
                                </TableRow>
                            </TableHead>
                            <TableBody>
                            </TableBody>
                        </Table>
                    </Paper>
                </Grid>
            </div>
        );
    }
}

export default OrdersList;